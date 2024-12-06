import CardApplicationBase from "./Base/CardApplicationBase";
import CardApplicationDocument from "./CardApplicationDocument";
import { CardDocumentStatusEnum } from "@enums/CardDocumentStatusEnum";
import { CardStatusEnum } from "@enums/CardStatusEnum";
import { InformTheUserError } from "@/errors/InformTheUserError";

export class CardApplication extends CardApplicationBase {
	get canBeEdited() {
		return [
			CardStatusEnum.TEMPORARY_SAVED,
			CardStatusEnum.SUBMITTED,
			CardStatusEnum.INCOMPLETE,
		]
			.includes( this.card_last_update?.status );
	}
	
	get isEditing() {
		return CardStatusEnum.TEMPORARY_SAVED === this.card_last_update?.status;
	}
	
	/**
	 * Adds a new document file to the card application.
	 * Sends the document data to the backend via a POST request.
	 *
	 * @param {CardApplicationDocument} document - The document to be added.
	 * @returns {Promise<axios.AxiosResponse<any>>} A promise that resolves when the request is completed.
	 */
	addNewFile( document ) {
		if ( this.card_application_document ) this.card_application_document.push(
			document ); else this.card_application_document = [ document ];
		// Prepare request data
		const params = new FormData();
		params.append( "file", document.file );
		params.append( "description", document.description );
		
		// Define API endpoint
		const url = this.route( "document.store", { cardApplication : this.id } );
		
		// Send POST request
		return this.$axios.post( url, params, {
			headers : { "Content-Type" : "multipart/form-data" },
		} )
		           .then( response => {
			           // Update document status if successfully submitted
			           document.id = response.data.id;
			           document.status = CardDocumentStatusEnum.SUBMITTED;
			           return response; // Return response if needed elsewhere
		           } );
	}
	
	/**
	 * Deletes a document from the card application.
	 * If the document has no unsaved changes, it removes the document from the card application array.
	 *
	 * @param {CardApplicationDocument} document - The document to be deleted.
	 */
	deleteFile( document ) {
		document.delete();
		// Skip removal if the document has unsaved changes on the server side
		if ( document.change ) return;
		
		// Find the document in the array and remove it
		const index = this.card_application_document.findIndex( file => file === document );
		console.log( "CardApplication.deleteFile", index );
		
		this.card_application_document.splice( index, 1 );
	}
	
	/**
	 * Retrieves the documents for the card application.
	 * If already fetched, returns the cached data. Otherwise, fetches it from the backend.
	 *
	 * @returns {Promise<PropertyType<Array<PropertyType<CardApplicationDocument>>>>}
	 */
	async getDocuments() {
		if ( this.card_application_document !== undefined ) return this.card_application_document;
		this.card_application_document = await CardApplicationDocument.fetchDocumentsByApplicationId( this.id );
		return this.card_application_document;
		
		
	}
	
	requestToEdit() {
		if ( this.canBeEdited ) {
			this.$axios.get( this.route( "cardApplication.edit", this.id ) )
			    .then( ( response ) => {
				    this.card_last_update = response.data;
				    this.updated_at = response.data.updated_at;
			    } )
			    .catch( ( error ) => {
				    const message = ( error.response?.status === 403 )
				                    ? "canNotEdit"
				                    : "somethingWentWrong";
				    
				    throw new InformTheUserError( {
					                                  message : message,
				                                  } );
			    } );
		} else {
			throw new InformTheUserError( {
				                              message : "canNotEdit",
			                              } );
		}
	}
}

export default CardApplication;

import CardApplicationBase from "./Base/CardApplicationBase";
import CardApplicationDocument from "./CardApplicationDocument";
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
	 * @returns {Promise<import("axios").AxiosResponse<any>>} A promise that resolves when the request is completed.
	 */
	addNewFile( document ) {
		if ( this.card_application_document ) this.card_application_document.push(
			document ); else this.card_application_document = [ document ];
		// Prepare request data
		return document.create( this.id );
	}
	
	/**
	 * Deletes a document from the card application.
	 * If the document has no unsaved changes, it removes the document from the card application array.
	 *
	 * @param {CardApplicationDocument} document - The document to be deleted.
	 */
	deleteFile( document ) {
		document.delete();
		// if the document is on the server don't remove it because need te update
		if ( !document.isNew ) return;
		
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
	
	/**
	 *
	 * @returns {Promise<import("axios").AxiosResponse<Object>>}
	 */
	requestToEdit() {
		if ( this.canBeEdited ) {
			return this.$axios.get( this.route( "cardApplication.edit", this.id ) )
			    .then( ( response ) => {
				    this.updated_at = response.data.card_last_update.updated_at;
				    return response.data.card_last_update;
			    } )
			    .catch( ( error ) => {
				    const message = ( error.response?.status === 403 )
				                    ? "canNotEdit"
				                    : "somethingWentWrong";
				    console.error( error );
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
	
	constructor( data ) {
		super( data );
		this.#receivingNewCardUpdate();
	}
	
	get card_last_update() {
		return super.card_last_update;
	}
	
	set card_last_update( value ) {
		super.card_last_update = value;
		this.#receivingNewCardUpdate();
	}
	
	#receivingNewCardUpdate() {
		if ( this.card_last_update.card_application_staff_id )
			this._card_staff_update_latest = this.card_last_update;
		else
			this._card_applicant_update_latest = this.card_last_update;
	}
}

export default CardApplication;

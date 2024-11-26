import CardApplicationBase from "./Base/CardApplicationBase";
import CardApplicationDocument from "./CardApplicationDocument.js";
import { CardDocumentStatusEnum } from "@enums/CardDocumentStatusEnum.js";

export class CardApplication extends CardApplicationBase {
	/**
	 * Adds a new document file to the card application.
	 * Sends the document data to the backend via a POST request.
	 *
	 * @param {CardApplicationDocument} document - The document to be added.
	 * @returns {Promise<axios.AxiosResponse<any>>} A promise that resolves when the request is completed.
	 */
	addNewFile( document ) {
		this.card_application_document.push( document );
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
		if ( document.change )
			return;
		
		// Find the document in the array and remove it
		const index = this.card_application_document.findIndex( file => file === document );
		console.log( "CardApplication.deleteFile", index );
		
		this.card_application_document.splice( index, 1 );
	}
	
	/**
	 * Retrieves the documents for the card application.
	 * If already fetched, returns the cached data. Otherwise, fetches it from the backend.
	 *
	 * @returns {Promise<axios.AxiosResponse<Array<CardApplicationDocument>>>}
	 */
	async getDocuments() {
		if ( this.card_application_document === undefined )
			this.card_application_document = await CardApplicationDocument.fetchDocumentsByApplicationId( this.id );
		return this.card_application_document;
	}
}

export default CardApplication;

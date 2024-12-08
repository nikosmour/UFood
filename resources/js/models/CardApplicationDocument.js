import CardApplicationDocumentBase from "./Base/CardApplicationDocumentBase";
import { CardDocumentStatusEnum } from "@enums/CardDocumentStatusEnum";

/**
 * Class representing a CardApplicationDocument model.
 * @class
 * @extends CardApplicationDocumentBase
 */
export class CardApplicationDocument extends CardApplicationDocumentBase {
	/**
	 * @static
	 * @type {string}
	 */
	static _CREATE = "create";
	
	/**
	 * @static
	 * @type {string}
	 */
	static _DELETE = "delete";
	
	/**
	 * @static
	 * @type {string}
	 */
	static _EDIT = "edit";
	
	/**
	 * @static
	 * @type {string}
	 */
	static _UPDATE_STATUS = "updateStatus";
	
	isDeleted = false;
	/**
	 * Extend the `prepareProperties` method to include new properties.
	 * @param {any} data - The data of the object.
	 */
	constructor( data ) {
		super( data );
		this.file = data.file ?? null;
		this.syncCurrent();
	}
	
	/**
	 * @private
	 * @type {?string}
	 */
	_change = null;
	
	/**
	 * Get the value of `change`.
	 * @type {?string}
	 */
	get change() {
		const changes = this.dirty;
		if ( !this.id )
			return ( !this.isDeleted )
			       ?
			       CardApplicationDocument._CREATE
			       :
			       null;
		else if ( this.isDeleted )
			return CardApplicationDocument._DELETE;
		else if ( changes[ "description" ] )
			return CardApplicationDocument._EDIT;
		else if ( changes[ "status" ] )
			return CardApplicationDocument._UPDATE_STATUS;
		return null; // has submitted
	}
	
	/**
	 * Get the value of `status`
	 * @return {CardDocumentStatusEnum} - The new status value.
	 */
	get status() {
		return super.status;
	}
	
	/**
	 * Set the value of `status` and mark the change as "updateStatus".
	 * @param {CardDocumentStatusEnum|string} status - The new status value or the key for it.
	 */
	set status( status ) {
		console.log( "cardApplicationDocument set status", status, this.status );
		super.status = status;
		if ( super.status === CardDocumentStatusEnum.SUBMITTED )
			this.syncCurrent();
	}
	
	/**
	 * Fetches the document list from the backend and initializes related documents.
	 *
	 
	 * @returns {Promise<axios.AxiosResponse<Array<CardApplicationDocument>>>} A promise that resolves to the list of documents.
	 */
	static async fetchDocumentsByApplicationId( cardApplicationId ) {
		const url = this.route( "document.index", { cardApplication : cardApplicationId } );
		return await CardApplicationDocument.$axios.get( url )
		                                    .then( response => response.data.documents );
	}
	
	/**
	 * Create a new CardApplicationDocument instance.
	 * @static
	 * @param {Object} data - The data for the new document.
	 * @param {string} data.description - The document's description.
	 * @param {File} data.file - The file associated with the document.
	 * @returns {CardApplicationDocument} A new CardApplicationDocument instance.
	 */
	static newDocument( data ) {
		data = {
			description : data.description,
			file_name :   data.file.name,
			change :      CardApplicationDocument._CREATE,
			file :        data.file,
		};
		return new CardApplicationDocument( data );
	}
	
	/**
	 * Sends the document data to the backend via a POST request.
	 *
	 * @param {number} application_id - The  application_id that will be assosiate with the document
	 * @returns {Promise<import("axios").AxiosResponse<any>>} A promise that resolves when the request is completed.
	 */
	create( application_id ) {
		const params = new FormData();
		params.append( "file", this.file );
		params.append( "description", this.description );
		
		// Define API endpoint
		const url = this.route( "document.store", { cardApplication : application_id } );
		
		// Send POST request
		return this.$axios.post( url, params, {
			headers : { "Content-Type" : "multipart/form-data" },
		} )
		           .then( response => {
			           // Update document status if successfully submitted
			           this.id = response.data.id;
			           this.status = CardDocumentStatusEnum.SUBMITTED;
			           return response; // Return response if needed elsewhere
		           } );
	}
	
	/**
	 * Set the value of `status` to _delete.
	 */
	delete() {
		this.isDeleted = true;
	}
	
	properties() {
		return super.properties();
	}
	
}

export default CardApplicationDocument;

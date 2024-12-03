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
	
	/**
	 * Extend the `prepareProperties` method to include new properties.
	 * @param {any} data - The data of the object.
	 */
	constructor( data ) {
		super( data );
		// to not initiate  setter of the status;
		if ( data.status )
			this._status = this.initToEnum( CardDocumentStatusEnum, data.status );
		this._change = data.change ?? null;
		this.file = data.file ?? null;
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
		return this._change;
	}
	
	get description() {
		return super.description;
	}
	
	/**
	 * Set the value of `description` and mark the change as "edit".
	 * @param {string} updateValue - The new description value.
	 */
	set description( updateValue ) {
		super.description = updateValue;
		this._setChange( CardApplicationDocument._EDIT );
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
		const current = this.status;
		console.log( "cardApplicationDocument set status", status, this.status );
		super.status = status;
		if ( current === this.status ) return;
		const change = status !== CardDocumentStatusEnum.SUBMITTED
		               ? CardApplicationDocument._UPDATE_STATUS
		               : null;
		this._setChange( change );
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
	 * Set the value of `change` internally.
	 * @private
	 * @param {string|null} value - The new value of `change`.
	 * @returns {void}
	 */
	_setChange( value ) {
		const abort = value === this.change || ( this.change === CardApplicationDocument._CREATE && [
			CardApplicationDocument._EDIT,
			CardApplicationDocument._UPDATE_STATUS,
		].includes( value ) );
		if ( abort ) return;
		this._change = this.change !== CardApplicationDocument._CREATE || value !== CardApplicationDocument._DELETE
		               ? value
		               : null;
	}
	
	/**
	 * Set the value of `status` to _delete.
	 */
	delete() {
		this._setChange( CardApplicationDocument._DELETE );
	}
	
	properties() {
		return super.properties()
		            .filter( value => value !== "status" );
	}
	
}

export default CardApplicationDocument;

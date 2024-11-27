import CardApplicationDocumentBase from "./Base/CardApplicationDocumentBase";
import { CardDocumentStatusEnum } from "@enums/CardDocumentStatusEnum.js";

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
	 * @param {CardDocumentStatusEnum||string} status - The new status value or the key for it.
	 */
	set status( status ) {
		console.log( "cardApplicationDocument set status", status, this.status );
		if ( typeof status === "string" ) status = CardDocumentStatusEnum[ status ];
		if ( status === this.status ) return;
		this._status = status;
		const change = status !== CardDocumentStatusEnum.SUBMITTED
		               ? CardApplicationDocument._UPDATE_STATUS
		               : null;
		this._setChange( change );
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
		const abort = value === this.change || ( this.change === CardApplicationDocument._CREATE &&
		                                         [
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
	
	/**
	 * Fetches the document list from the backend and initializes related documents.
	 *
	 
	 * @returns {Promise<axios.AxiosResponse<Array<CardApplicationDocument>>>} A promise that resolves to the list of documents.
	 */
	static async fetchDocumentsByApplicationId( cardApplicationId ) {
		const url = this.route( "document.index", { cardApplication : cardApplicationId } );
		const documents = await CardApplicationDocument.$axios.get( url )
		                                               .then( response => response.data.documents );
		return documents.map(
			document => new CardApplicationDocument( document ),
		);
	}
	
	/**
	 * Extend the `prepareProperties` method to include new properties.
	 * @param {any} data - The data of the object.
	 */
	constructor( data ) {
		super( data );
		// to not initiate  setter of the status;
		this._status = data.status;
		this._change = data.change ?? null;
		this.file = data.file ?? null;
	}
	
	properties() {
		const props = super.properties();
		const index = props.findIndex( "status" );
		if ( index !== -1 )
			delete props[ index ];
		return props;
	}
	
}

export default CardApplicationDocument;

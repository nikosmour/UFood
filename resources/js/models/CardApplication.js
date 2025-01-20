import CardApplicationBase from "./Base/CardApplicationBase";
import CardApplicationDocument from "./CardApplicationDocument";
import { CardStatusEnum } from "@enums/CardStatusEnum";
import { InformTheUserError } from "@/errors/InformTheUserError";
import { EchoInstance } from "@/plugins/echo";

export class CardApplication extends CardApplicationBase {
	get canBeEdited() {
		return [
			CardStatusEnum.TEMPORARY_SAVED,
			CardStatusEnum.SUBMITTED,
			CardStatusEnum.INCOMPLETE,
		]
			.includes( this.card_last_update?.status );
	}
	
	canBeCheckedByStaff( userId = 0 ) {
		const status = this.card_last_update?.status;
		if ( CardStatusEnum.TEMPORARY_SAVED === this.card_last_update?.status ) return false;
		return !( status === CardStatusEnum.CHECKING && this.card_last_update.card_application_staff_id !== userId );
		
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
	requestToEdit( applicant = true ) {
		const check = applicant
		              ? this.canBeEdited
		              : this.canBeCheckedByStaff;
		if ( check ) {
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
		this.receivingNewCardUpdate();
	}
	
	get card_last_update() {
		return super.card_last_update;
	}
	
	set card_last_update( value ) {
		super.card_last_update = value;
		this.receivingNewCardUpdate();
	}
	
	get notBroadcast() {
		return [
			CardStatusEnum.TEMPORARY_SAVED,
			CardStatusEnum.ACCEPTED,
			CardStatusEnum.INCOMPLETE,
			CardStatusEnum.REJECTED,
		]
			.includes( this.card_last_update?.status );
	}
	
	broadcast( options = {} ) {
		super.broadcast( options );
		const echo = options.vue.$echo;
		const application = options.target;
		const $notify = options.vue.$notify;
		const channelName = `academic.${ this.academic_id }`;
		console.info( "cardApplicationBroadCasting" );
		echo
			.private( channelName )
			.listen( "CardApplicationUpdated", ( e ) => {
				console.info( "cardApplicationUpdate12", e, this );
				application.expiration_date = new Date( e[ "expiration_date" ] );
				application.card_last_update = e[ "cardApplicationUpdate" ];
				this.receivingNewCardUpdate();
				$notify( { error : " new status of your application " + application.card_last_update.status.key } );
			} );
	}
	
	receivingNewCardUpdate() {
		if ( this.card_last_update.card_application_staff_id )
			this._card_staff_update_latest = this.card_last_update;
		else
			this._card_applicant_update_latest = this.card_last_update;
	}
	
	stopBroadcast() {
		super.stopBroadcast();
		const channelName = `cardApplication.${ this.id }`;
		EchoInstance.private( channelName )
		            .stopListening( "CardApplicationUpdated" );
	}
}

export default CardApplication;

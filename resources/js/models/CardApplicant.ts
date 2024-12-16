import CardApplicantBase from "./Base/CardApplicantBase";
import type Address from "@models/Address";
import type Academic from "@models/Academic";
import type { AxiosInstance } from "axios";

/**
 * Class representing a CardApplicant model.
 * @class
 * @extends CardApplicantBase
 */
export class CardApplicant extends CardApplicantBase {
	
	/**
	 * Extend the constructor method to have valid_card_application be the same  object
	 * with  current_card_application in the event that hasn't sent from the server because it will
	 * be the same application.
	 * @param {any} data - The data of the object.
	 */
	constructor( data ) {
		super( data );
		this._valid_card_application ??= this.current_card_application;
	}
	
	/**
	 * Sends the user info data to the backend via a POST request to create or update the user.
	 *
	 * @param user - The current Academic user that is logging in.
	 * @returns {Promise<import("axios").AxiosResponse<any>>} A promise that resolves when the request is completed.
	 */
	static create( user : Academic ) : Promise<import("axios").AxiosResponse<any>> {
		console.info( "User dirty properties:", user.dirty );
		
		const params = {
			...user.dirty,
			...( user.card_applicant as CardApplicant ).dirty,
		};
		
		const addresses : Partial<{
			permanent : Partial<Address>;
			temporary : Partial<Address>;
		}> = {};
		
		for ( const address of user.card_applicant.addresses ) {
			if ( address.isDirty() ) {
				const type = address.is_permanent
				             ? "permanent"
				             : "temporary";
				addresses[ type ] = {
					location :     address.location,
					phone :        address.phone,
					is_permanent : address.is_permanent,
				};
			}
		}
		
		// Handle deletion of the 'permanent' address if all its fields are empty
		if (
			addresses.hasOwnProperty( "permanent" ) &&
			addresses[ "permanent" ]?.location === "" &&
			addresses[ "permanent" ]?.phone === ""
		) {
			delete addresses[ "permanent" ];
			params[ "delPermanent" ] = true;
		}
		
		// Include addresses in the params if any are dirty
		if ( Object.keys( addresses ).length !== 0 ) {
			params[ "addresses" ] = addresses;
		}
		
		// Define the API endpoint
		const url = this.route( "cardApplicant.store" );
		
		// Send POST request
		return ( this.$axios as AxiosInstance ).post( url, params )
		                                       .then( response => {
			                                       user.syncCurrent();
			                                       const card_applicant = user.card_applicant as CardApplicant;
			                                       card_applicant.syncCurrent();
			                                       card_applicant.current_card_application =
				                                       response.data.cardApplication;
			                                       const addresses = card_applicant.addresses as Address[];
			                                       if ( params[ "delPermanent" ] ) {
				                                       const index = addresses.findIndex(
					                                       address => address.is_permanent === true );
				                                       addresses.splice( index, 1 );
			                                       }
			                                       addresses.forEach( ( address ) => address.syncCurrent() );
		                                       } );
	}
	
	protected adjustInitializationData( data : T ) : T {
		if ( !data.hasOwnProperty( "addresses" ) || data[ "addresses" ] === undefined )
			return data;
		let addresses = ( data[ "addresses" ] ?? [] ) as { is_permanent : boolean }[];
		if ( addresses.filter( ( address ) => address[ "is_permanent" ] ).length === 0 )
			addresses.push( { is_permanent : true } );
		if ( addresses.length === 1 )
			addresses.push( { is_permanent : false } );
		data[ "addresses" ] = addresses;
		return data;
	}
}

export default CardApplicant;
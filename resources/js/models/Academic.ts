import AcademicBase from "./Base/AcademicBase";

/**
 * Class representing a Academic model.
 * @class
 * @extends AcademicBase
 */
export class Academic extends AcademicBase {
	public async prepareForApplicationCreate( byTheSystem : boolean = false ) : Promise<void> {
		const url = this.route( "cardApplicant.index" );
		if ( byTheSystem || this.card_applicant === undefined || this.card_applicant.addresses === undefined ) {
			let card_applicant = null;
			try {
				card_applicant = ( await this.$axios.get( url, { params : { byTheSystem } } ) ).data;
				
			} catch ( error ) {
				if ( error.response?.status === 404 ) {
					// its not found but it is returning  a new applicant with the information that we have
					//in the session
					card_applicant = error.response.data.applicant;
				}
			} finally {
				if ( card_applicant !== null ) {
					if ( this.card_applicant )
						this.card_applicant.updateSync( card_applicant );
					else
						this.card_applicant = card_applicant;
				}
			}
		}
		
	}
	
	public broadcast( options : Record<string, any> = {} ) {
		super.broadcast( options );
		const user = options.target;
		user.card_applicant?.current_card_application?.broadcast(
			{ target : user.card_applicant?.current_card_application } );
	}
	
	public stopBroadcast() {
		super.stopBroadcast();
		this.card_applicant?.current_card_application?.stopBroadcast();
	}
}

export default Academic;
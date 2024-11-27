import CardApplicantBase from "./Base/CardApplicantBase";

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
		this.valid_card_application ??= this.current_card_application;
	}
}

export default CardApplicant;
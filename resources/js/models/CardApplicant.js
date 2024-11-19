import CardApplicantBase from "./Base/CardApplicantBase";

/**
 * Class representing a CardApplicant model.
 * @class
 * @extends CardApplicantBase
 */
export class CardApplicant extends CardApplicantBase {
	
	/**
	 * Extend the `prepareProperties` method to have valid_card_application be the same  object
	 * with  current_card_application in the event that hasn't sent from the server because it will
	 * be the same application.
	 * @param {Object} data - The data of the object.
	 * @returns {Object} An object containing initialized properties, including the formatted ID.
	 */
	prepareProperties( data ) {
		const baseData = super.prepareProperties( data );
		baseData[ "valid_card_application" ] =
			baseData[ "valid_card_application" ] ?? baseData[ "current_card_application" ];
		
		// Extend or modify the properties from the base class
		return baseData;
	}
}

export default CardApplicant;
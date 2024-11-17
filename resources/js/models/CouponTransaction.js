import CouponTransactionBase from "./Base/CouponTransactionBase";

/**
 * Class representing a CouponTransaction model with a custom ID format.
 * @class
 * @extends CouponTransactionBase
 * @property {string|null} id - Custom ID formatted as {type}{raw_id}, where type is 'P', 'T', or 'U'.
 */
export class CouponTransaction extends CouponTransactionBase {
	
	
	/**
	 * Calculates and returns the custom formatted ID for a specific transaction type.
	 * @param {number} id - Raw ID of the transaction.
	 * @param {string|null} transactionType - Type of transaction (e.g., 'buying', 'sending').
	 * @returns {string|null} Formatted ID as {type}{id}, or `null` if `id` or `transactionType` is invalid.
	 */
	_calculateID( id, transactionType ) {
		if ( id === null || !transactionType ) return null;
		
		let type = null;
		switch ( transactionType ) {
			case "buying":
				type = "P";
				break;
			case "sending":
			case "receiving":
				type = "T";
				break;
			case "using":
				type = "U";
				break;
		}
		return type
		       ? `${ type }${ id }`
		       : null;
	}
	
	/**
	 * Extend the `prepareProperties` method to include the custom ID calculation.
	 * @param {Object} data - The data of the object.
	 * @returns {Object} An object containing initialized properties, including the formatted ID.
	 */
	prepareProperties( data ) {
		const baseData = super.prepareProperties( data );
		
		// Extend or modify the properties from the base class
		return {
			...baseData,
			id : this._calculateID( baseData.id, baseData.transaction ), // Custom formatted ID
		};
	}
}

export default CouponTransaction;
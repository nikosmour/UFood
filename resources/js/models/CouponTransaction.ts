import CouponTransactionBase from "./Base/CouponTransactionBase";
import { InvalidModelDataError } from "@/errors/InvalidDataError";

/**
 * Class representing a CouponTransaction model with a custom ID format.
 * @class
 * @extends CouponTransactionBase
 * @property {PropertyType<string>} id - Custom ID formatted as {type}{raw_id}, where type is 'P', 'T', or 'U'.
 */
export class CouponTransaction extends CouponTransactionBase {
	constructor( data : any ) {
		super( data );
		this._foul_id = this.#calculateID( super.id, this.transaction ); // Custom formatted ID
	}
	
	private _foul_id : PropertyType<string>;
	
	get foul_id() : PropertyType<string> {
		return this._foul_id;
	}
	
	/**
	 * Calculates and returns the custom formatted ID for a specific transaction type.
	 * @paramid - Raw ID of the transaction.
	 * @paramtransactionType - Type of transaction (e.g., 'buying', 'sending').
	 * @returns Formatted ID as {type}{id}, or `null` if `id` or `transactionType` is invalid.
	 */
	#calculateID( id : PropertyType<number>, transactionType : PropertyType<string> ) : PropertyType<string> {
		if ( id === null || !transactionType ) return null;
		if ( id === undefined ) return undefined;
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
			default:
				break;
		}
		if ( type !== null ) return `${ type }${ id }`;
		throw new InvalidModelDataError( {
			                                 "message" : "undefined category of coupon Transactions",
			                                 "type" :    "string",
		                                 } );
	}
}

export default CouponTransaction;
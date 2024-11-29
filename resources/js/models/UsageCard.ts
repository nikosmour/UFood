import UsageCardBase from "./Base/UsageCardBase";

/**
 * Class representing a UsageCard model.
 * @class
 * @extends UsageCardBase
 */
export class UsageCard extends UsageCardBase {
	private readonly _date : PropertyType<Date> = undefined;
	
	constructor( data : any ) {
		super( data );
		console.log( data );
		this._date = this.initToDate( data.datetime );
	}
	
	get date() : PropertyType<Date> {
		return this._date;
	}
	
}

export default UsageCard;
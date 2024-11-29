interface InvalidDataErrorInterface {
	field? : string;
	message? : string;
	type : string;
}

export class InvalidModelDataError extends Error {
	public statusCode : number;
	public field? : string;
	
	constructor( options : InvalidDataErrorInterface ) {
		super( options.message ?? ( "invalid " + options.type ) );
		this.name = "InvalidModelDataError";
		this.statusCode = 400;  // Common for bad data
		this.field = options.field;
		
		Object.setPrototypeOf( this, new.target.prototype );
	}
}
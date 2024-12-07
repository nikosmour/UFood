interface DevelopmentErrorInterface {
	message : string;
}

export class DevelopmentError extends Error {
	public statusCode : number;
	
	constructor( options : DevelopmentErrorInterface ) {
		super( options.message );
		this.name = "DevelopmentError";
		this.statusCode = 400;  // Common for bad data
		Object.setPrototypeOf( this, new.target.prototype );
	}
}
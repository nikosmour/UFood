/**the `message` should be  the unTranslated key */
type InvalidErrorInterface = {
	message? : string;
}

export class InformTheUserError extends Error {
	public statusCode : number;
	public field? : string;
	
	constructor( options : InvalidErrorInterface ) {
		super( options.message ?? "somethingWentWrong" );
		this.name = "InformTheUserError";
		this.statusCode = 413;
		Object.setPrototypeOf( this, new.target.prototype );
	}
}
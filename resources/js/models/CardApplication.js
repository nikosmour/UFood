import CardApplicationBase from "./Base/CardApplicationBase";


export class CardApplication extends CardApplicationBase {
	
	addNewFile( document ) {
		this.card_application_document.push( document );
	}
	
	/**
	@param {CardApplicationDocument} document
	 */
	deleteFile( document ) {
		document.delete();
		if ( document.change )
			return;
		
		const index = this.card_application_document.findIndex( file => file === document );
		console.log( "CardApplication. deleteFile", index );
		
		this.card_application_document.splice( index, 1 );
	}
}

export default CardApplication;
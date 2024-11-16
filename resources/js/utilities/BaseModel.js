class BaseModel {
	/**
	 * Construct a model instance from raw data.
	 * @param {Object} data - The raw data from the server.
	 */
	constructor( data = {} ) {
		this.fill( data );
	}
	
	/**
	 * Fill the model properties with data.
	 * @param {Object} data - The raw data to assign to the model.
	 */
	fill( data ) {
		Object.keys( data )
		      .forEach( ( key ) => {
			      this[ key ] = data[ key ];
		      } );
	}
	
	/**
	 * Serialize the model instance to JSON.
	 * @returns {string} JSON string representation of the model.
	 */
	toJSON() {
		return JSON.stringify( this.toObject() );
	}
	
	/**
	 * Convert the model instance to a plain object.
	 * @returns {Object} Plain object representation of the model.
	 */
	toObject() {
		return { ...this };
	}
}

export default BaseModel;

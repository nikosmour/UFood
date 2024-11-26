class BaseModel {
	static $axios = null;
	static route = null;
	
	/**
	 * Get the Axios instance.
	 * @returns {axios.AxiosInstance} The Axios instance.
	 */
	get $axios() {
		return this.constructor.$axios;
	}
	
	/**
	 * Get the Ziggy route helper function.
	 * @returns {Function} The route helper.
	 */
	get route() {
		return this.constructor.route;
	}
	
	/**
	 * Set up dependencies for the BaseModel.
	 * @param {Function} route - The Ziggy route helper function.
	 * @param {axios.AxiosInstance} axios - The Axios instance.
	 */
	static setup( route, axios ) {
		this.route = route;
		this.$axios = axios;
	}
	
	/**
	 * Initializes an array of related objects if data exists.
	 *
	 * This method maps an array of raw data objects to an array of instantiated objects of the specified class.
	 *
	 * @param {Array<Object>|null} data - The raw data to initialize objects with, or null if no data is available.
	 * @param {typeof BaseModel} ClassRef - The class constructor to instantiate each object.
	 * @returns {Array<BaseModel>} An array of instantiated objects of the specified class.
	 */
	initRelatedArray( data, ClassRef ) {
		return Array.isArray( data )
		       ? data.map( item => new ClassRef( item ) )
		       : undefined;
	}
	
	/**
	 * Initialize a related object if data exists.
	 * @param {Object|null} data - The data to initialize the object with.
	 * @param {typeof BaseModel} ClassRef - The class to instantiate.
	 * @returns {BaseModel|null} The initialized object or null.
	 */
	initRelatedObject( data, ClassRef ) {
		return data
		       ? new ClassRef( data )
		       : data;
	}
	
	/**
	 * Safely convert a value to a Boolean.
	 * @param {*} value - The value to convert.
	 * @returns {Boolean|null} The converted number or null if invalid.
	 */
	initToBoolean( value ) {
		return value
		       ? Boolean( value )
		       : null;
	}
	
	/**
	 * Safely convert a value to a Date.
	 * @param {*} value - The value to convert.
	 * @returns {Date|null} The converted number or null if invalid.
	 */
	initToDate( value ) {
		return value
		       ? new Date( value )
		       : null;
	}
	
	/**
	 * Safely convert a value to a EnumUnit of the correspond enumClass statics Value .
	 * @param {typeof BaseEnum} enumClass - The enum
	 * @param {*} value - The value to convert.
	 * @returns {EnumUnit|null} The converted number or null if invalid.
	 */
	initToEnum( enumClass, value ) {
		return value
		       ? enumClass.findByValue( value )
		       : null;
	}
	
	/**
	 * Safely convert a value to a number.
	 * @param {*} value - The value to convert.
	 * @returns {number|null} The converted number or null if invalid.
	 */
	initToNumber( value ) {
		return value
		       ? Number( value )
		       : null;
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

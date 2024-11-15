/**
 * EnumUnit represents a single unit in an enum, consisting of a key and a value.
 * It includes methods for accessing the key, value, and a string representation of the enum unit.
 */
export class EnumUnit {
	
	/**
	 * Creates an instance of EnumUnit with a specific key and value.
	 *
	 * @param {string} name - The name of the enum key.
	 * @param {any} value - The value associated with the enum key.
	 */
	constructor( name, value ) {
		/** @private */
		this._key = name;
		/** @private */
		this._value = value;
		Object.freeze( this ); // Make the instance immutable
	}
	
	/**
	 * Gets the key of the current enum instance.
	 *
	 * @returns {string} - The key of the enum instance.
	 */
	get key() {
		return this._key;
	}
	
	/**
	 * Gets the value of the current enum instance.
	 *
	 * @returns {any} - The value of the enum instance.
	 */
	get value() {
		return this._value;
	}
	
	/**
	 * Returns a string representation of the enum unit in the form `key: value`.
	 *
	 * @returns {string} - A string representation of the enum unit.
	 */
	toString() {
		return `${ this.key }: ${ this.value }`;
	}
}
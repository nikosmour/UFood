type PropertyType<T> = T | undefined | null;

export interface IEnumUnit<T = any> {
	/**
	 * Gets the key of the current enum instance.
	 *
	 * @returns {string} - The key of the enum instance.
	 */
	key : string;
	/**
	 * Gets the value of the current enum instance.
	 *
	 * @returns {any} - The value of the enum instance.
	 */
	value : T;
	
	/**
	 * Returns a string representation of the enum unit in the form `key: value`.
	 *
	 * @returns {string} - A string representation of the enum unit.
	 */
	toString() : string;
}
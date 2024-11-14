// BaseEnum.js

import { EnumUnit } from "./EnumUnit.js";

/**
 * BaseEnum is a class for creating and managing enumerated values.
 * It takes an object of key-value pairs and converts them into `EnumUnit` instances.
 * It provides utility methods to work with enums like getting keys, values,
 * and finding corresponding enum units by their key or value.
 */
export class BaseEnum {

    /**
     * @param {Object} enumEntries - An object containing key-value pairs to initialize the enum.
     * The keys are the enum keys, and the values are the associated values.
     */

    /*
        constructor(enumEntries) {
            // Initialize each entry as an EnumUnit instance
            for (const [key, value] of Object.entries(enumEntries)) {
                this[key] = new EnumUnit(key, value);
            }
        }
    */

    /**
     * Finds the EnumUnit associated with a given key in the enum.
     *
     * @param {string} key - The key to search for.
     * @returns {EnumUnit|null} - The corresponding EnumUnit if found, otherwise null.
     */
    static findByKey(key) {
        return this[key] || null;
    }

    /**
     * Finds the EnumUnit associated with a given value in the enum.
     *
     * @param {any} value - The value to search for.
     * @returns {EnumUnit|null} - The corresponding EnumUnit if found, otherwise null.
     */
    static findByValue(value) {
        return Object.values(this).find(unit => unit.value === value) || null;
    }

    /**
     * Finds the EnumUnits associated with a given values in the enum.
     *
     * @param {any[]} values - The value to search for.
     * @returns {EnumUnit|null[]} - The corresponding EnumUnit if found, otherwise null.
     */
    static findByValueMany(values) {
        const array = [];
        for (const value of values) {
            array.push(this.findByValue(value));
        }
        return array;
    }

    /**
     * Gets all the keys in the enum.
     *
     * @returns {string[]} - An array of all enum keys.
     */
    static keys() {
        return Object.keys(this).filter(key => this[key] instanceof EnumUnit);
    }

    /**
     * Gets all the values in the enum.
     *
     * @returns {any[]} - An array of all enum values.
     */
    static values() {
        return this.keys().map(key => this[key].value);
    }

    /**
     * Converts the enum to an array of its instances (EnumUnit objects).
     *
     * @returns {EnumUnit[]} - An array of all EnumUnit instances.
     */
    static toArray() {
        return this.keys().map(key => this[key]);
    }
}

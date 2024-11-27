import {EnumUnit} from "@/utilities/enums/EnumUnit";

export abstract class BaseEnum extends EnumUnit {
    /**
     * Finds the EnumUnit associated with a given key in the enum.
     * @param key - The key to search for.
     * @returns The corresponding EnumUnit if found, otherwise null.
     */
    static findByKey<T extends typeof BaseEnum>(this: T, key: string): InstanceType<T> | null {
        const unit = (this as any)[key];
        return unit instanceof this ? (unit as InstanceType<T>) : null;
    }

    /**
     * Finds the EnumUnit associated with a given value in the enum.
     * @param value - The value to search for.
     * @returns The corresponding EnumUnit if found, otherwise null.
     */
    static findByValue<T extends typeof BaseEnum>(this: T, value: any): InstanceType<T> | null {
        const enumUnits = Object.values(this).filter(item => item instanceof this) as InstanceType<T>[];
        return enumUnits.find(unit => unit.value === value) || null;
    }

    /**
     * Finds multiple EnumUnits by their values.
     * @param values - An array of values to search for.
     * @returns An array of corresponding EnumUnits.
     */
    static findByValueMany<T extends typeof BaseEnum>(this: T, values: any[]): (InstanceType<T> | null)[] {
        return values.map(value => this.findByValue(value));
    }

    /**
     * Gets all the keys in the enum.
     * @returns An array of all enum keys.
     */
    static keys<T extends typeof BaseEnum>(this: T): string[] {
        return Object.keys(this).filter(key => (this as any)[key] instanceof this);
    }

    /**
     * Converts the enum to an array of its instances (EnumUnit objects).
     * @returns An array of all T instances.
     */
    static toArray<T extends typeof BaseEnum>(this: T): InstanceType<T>[] {
        return this.keys().map(key => (this as any)[key]) as InstanceType<T>[];
    }

    /**
     * Gets all the values in the enum.
     * @returns An array of all enum values.
     */
    static values<T extends typeof BaseEnum>(this: T): any[] {
        return this.toArray().map(unit => unit.value);
    }
}

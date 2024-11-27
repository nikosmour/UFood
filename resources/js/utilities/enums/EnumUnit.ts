import {IEnumUnit} from "@/types";

export class EnumUnit<T = any> implements IEnumUnit<T> {
    private readonly _key: string;
    private readonly _value: T;

    constructor(name: string, value: T) {
        this._key = name;
        this._value = value;
        Object.freeze(this); // Make the instance immutable
    }

    get key(): string {
        return this._key;
    }

    get value(): T {
        return this._value;
    }

    toString(): string {
        return `${this.key}: ${this.value}`;
    }
}

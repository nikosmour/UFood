import type { IEnumUnit } from "@types/globals";


export class EnumUnit<T = any> implements IEnumUnit<T> {
    readonly #key : string;
    readonly #value : T;

    constructor(name: string, value: T) {
        this.#key = name;
        this.#value = value;
        Object.freeze(this); // Make the instance immutable
    }

    get key(): string {
        return this.#key;
    }

    get value(): T {
        return this.#value;
    }

    toString(): string {
        return `${this.key}: ${this.value}`;
    }
}

import type { AxiosInstance } from "axios";
import type { route } from "ziggy-js";
import type { BaseEnum } from "@/utilities/enums/BaseEnum";
import { InvalidModelDataError } from "@/errors/InvalidDataError";
import ChangeTracker from "@utilities/ChangeTracker";
import { config } from "@/config";

/**
 * Base class for models in the application.
 * Provides utility methods and setup for Axios and Ziggy routes.
 */
export default abstract class BaseModel<TData extends Pick<TInterface, keyof TInterface>, TInterface extends Record<string, any>> extends ChangeTracker {
	static $axios : AxiosInstance | null = null;
	static route : typeof route | null = null;
	
	/**
	 * Serialize the model instance to JSON.
	 * @returns {string} JSON string representation of the model.
	 */
	public toJSON() : string {
		return JSON.stringify( this.toObject() );
	}
	
	/**
	 * Converts the model to a plain object representation.
	 */
	public toObject() : TInterface {
		const obj : Partial<TInterface> = {};
		this.properties()
		    .forEach( ( prop ) => {
			    obj[ prop ] = this[ prop ];
		    } );
		this.relationships()
		    .forEach( ( prop ) => {
			    const value = this[ prop ];
			    if ( value instanceof BaseModel ) {
				    obj[ prop ] = value.copy();
			    } else if ( Array.isArray( value ) ) {
				    obj[ prop ] = value.map( ( item ) =>
					                             item instanceof BaseModel
					                             ? item.copy()
					                             : item,
				    );
			} else {
				    obj[ prop ] = value;
			}
		    } );
		return obj as TInterface;
	}
	
	/**
	 * Creates a copy of the model.
	 */
	public copy() : this {
		return new ( this.constructor as { new( ...args : any[] ) : this } )(
			this.toObject(),
		);
	}
	
	
	/**
	 * Get the Axios instance.
	 * @returns {AxiosInstance} The Axios instance.
	 */
	get $axios() : AxiosInstance {
		if ( !BaseModel.$axios ) {
			throw new Error( "Axios instance is not set up. Call BaseModel.setup first." );
		}
		return BaseModel[ "$axios" ]!;
	}
	
	/**
	 * Get the Ziggy route helper function.
	 * @returns {typeof route} The route helper.
	 */
	get route() : typeof route {
		if ( !BaseModel[ "route" ] ) {
			throw new Error( "Route helper is not set up. Call BaseModel.setup first." );
		}
		return BaseModel[ "route" ]!;
	}
	
	/**
	 * Set up dependencies for the BaseModel.
	 * @param {typeof route} routeI - The Ziggy route helper function.
	 * @param {AxiosInstance} axios - The Axios instance.
	 */
	static setup( routeI : typeof route, axios : AxiosInstance ) : void {
		this.route = routeI;
		this.$axios = axios;
	}
	
	/**
	 * Check if the model or its relationships have unsaved changes.
	 *
	 * @param {number} level - The depth to search for changes in relationships.
	 *                         Use 0 for infinite depth, or a positive number to limit the depth.
	 * @returns {boolean|null} - Returns:
	 *                           - `true` if the model or any of its relationships are dirty.
	 *                           - `false` if neither the model nor its relationships are dirty.
	 *                           - `null` if the depth is limited and the level is exhausted.
	 */
	isDirtyDeep( level : number = 0 ) : boolean | null {
		// Check if the current model has changes
		if ( --level === 0 )
			return null;
		if ( this.isDirty() ) {
			return true;
		}
		
		// Check if any related models are dirty
		for ( const relationship of this.relationships() ) {
			const relatedValue = this[ relationship ] as unknown;
			
			if ( relatedValue instanceof BaseModel && relatedValue.isDirtyDeep( level ) ) {
				return true;
			} else if ( Array.isArray( relatedValue ) ) {
				// Check each item in the array for dirty state
				if ( relatedValue.some( item => item instanceof BaseModel && item.isDirtyDeep( level ) ) ) {
					return true;
				}
			}
		}
		
		// If neither the model nor its relationships are dirty, return false
		return false;
	}
	
	/**
	 * Check if the model or its relationships are clean (no unsaved changes).
	 *
	 * @param {number} level - The depth to search for changes in relationships.
	 *                         Use 0 for infinite depth, or a positive number to limit the depth.
	 * @returns {boolean|null} - Returns:
	 *                           - `true` if the model and all its relationships are clean (no changes).
	 *                           - `false` if the model or any of its relationships are dirty.
	 *                           - `null` if the depth is limited and the level is exhausted.
	 */
	isCleanDeep( level : number = 0 ) : boolean | null {
		// Use isDirtyDeep to check if the model or its relationships are dirty.
		const isDirtyDeep = this.isDirtyDeep( level );
		
		// If isDirtyDeep returns null, stop recursion and return null
		if ( isDirtyDeep === null ) {
			return null;
		}
		
		// If isDirtyDeep is false, then the model and its relationships are clean
		return !isDirtyDeep;
	}
	
	/**
	 * Returns an array of properties that define the model.
	 */
	protected abstract properties() : Array<keyof TInterface>;
	
	/**
	 * Returns an array of relationships associated with the model.
	 */
	protected abstract relationships() : Array<keyof TInterface>;
	
	/**
	 * Initializes an array of related objects if data exists.
	 */
	protected initRelatedArray<T extends BaseModel<any, any>>(
		data : ConstructorParameters<new ( data : any ) => T>[0][] | null,
		ClassRef : new ( data : ConstructorParameters<new ( data : any ) => T>[0] ) => T,
	) : T[] | null {
		return data
		       ? data.map( ( item ) => new ClassRef( item ) )
		       : null;
	}
	
	/**
	 * Initializes a related object if data exists.
	 */
	protected initRelatedObject<T extends BaseModel<any, any>>(
		data : ConstructorParameters<new ( data : any ) => T>[0] | null,
		ClassRef : new ( data : ConstructorParameters<new ( data : any ) => T>[0] ) => T,
	) : T | null {
		return data
		       ? new ClassRef( data )
		       : null;
	}
	
	/**
	 * Utility to initialize a boolean value.
	 */
	protected initToBoolean( value : any ) : boolean {
		if ( value === null || value === undefined )
			throw new InvalidModelDataError( { type : "boolean" } );
		return Boolean( value );
	}
	
	/**
	 * Utility to initialize a date value.
	 */
	protected initToDate( value : any ) : Date {
		const date = new Date( value );
		if ( isNaN( date.getTime() ) )
			throw new InvalidModelDataError( { type : "date" } );
		return date;
	}
	
	/**
	 * Utility to initialize an enum value.
	 */
	protected initToEnum<E extends typeof BaseEnum>(
		enumClass : E,
		value : any,
	) : InstanceType<E> | null {
		if ( value instanceof enumClass ) return value;
		const enumI = enumClass.findByValue( value );
		if ( enumI ) return enumI;
		throw ( new InvalidModelDataError( { type : enumClass.name } ) );
	}
	
	/**
	 * Initializes an array of related objects if data exists.
	 *
	 * @param data - The raw data to initialize objects with, or null if no data is available.
	 * @returns  An array of instantiated objects of the specified class.
	 */
	protected initiation( data : TData ) : void {
		const fillableProperties = [
			...this.properties(),
			...this.relationships(),
		];
		Object.assign(
			this,
			Object.fromEntries(
				Object.entries( data )
				      .filter( ( [ key, value ] ) => value !== null && value !== undefined &&
				                                     fillableProperties.includes( key as keyof TInterface ),
				      ),
			),
		);
		this.syncCurrent();
	}
	
	/**
	 * Utility to initialize a number value.
	 */
	protected initToNumber( value : any ) : number {
		const num = Number( value );
		if ( isNaN( num ) ) throw new InvalidModelDataError( { type : "number" } );
		return num;
	}
	
	
	public trackableProps<T extends this>() : Array<keyof T> {
		return config.alterableProperties[ this.constructor.name ] as Array<keyof T> | null ?? [] as Array<keyof T>;
	}
	
}


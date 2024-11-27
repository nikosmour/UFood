import type { AxiosInstance } from "axios";
import type route from "ziggy-js";
import type { BaseEnum } from "@/utilities/enums/BaseEnum";
import { InvalidModelDataError } from "@/errors/InvalidDataError";

/**
 * Base class for models in the application.
 * Provides utility methods and setup for Axios and Ziggy routes.
 */
export default abstract class BaseModel<TData extends Pick<TInterface, keyof TInterface>
	, TInterface> {
	static $axios : AxiosInstance | null = null;
	static route : typeof route | null = null;
	
	/**
	 * Seria= undefinedlize the model instance to JSON.
	 * @returns {string} JSON string representation of the model.
	 */
	public toJSON() : string {
		return JSON.stringify( this.toObject() );
	}
	
	/**
	 * Convert the model instance to a plain object.
	 * @returns {Record<string, any>} Plain object representation of the model.
	 */
	public toObject() : Record<string, any> {
		const d = this as unknown as TInterface;
		const properties = this.properties() as Array<keyof TInterface>;
		const temp = properties.reduce( ( acc, prop ) => {
			acc[ prop ] = d[ prop ];
			return acc;
		}, {} as TInterface );
		
		const relationships = this.relationships() as Array<keyof TInterface>;
		relationships.forEach( ( prop ) => {
			const relatedValue = d[ prop ] as PropertyType<InstanceType<BaseModel<any, any>> | InstanceType<BaseModel<any, any>>[]>;
			if ( relatedValue instanceof BaseModel ) {
				temp[ prop ] = relatedValue.copy() as TInterface[keyof TInterface]; // Ensure it's a copy of the relationship
			} else if ( Array.isArray( relatedValue ) ) {
				temp[ prop ] = relatedValue.map( ( item ) =>
					                                 item instanceof BaseModel
					                                 ? item.copy()
					                                 : item,
				) as TInterface[keyof TInterface];
			} else {
				temp[ prop ] = relatedValue; // Fallback for other cases
			}
		} );
		
		return temp as Record<string, any>;
	}
	
	/**
	 * Convert the model instance to a plain object.
	 * @returns  A copy  of the model.
	 */
	public copy<T extends this>() : T {
		return new ( this.constructor as { new( ...args : any[] ) : T } )( this.toObject() );
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
	 * Initializes an array of related objects if data exists.
	 *
	 * @param data - The raw data to initialize objects with, or null if no data is available.
	 * @returns  An array of instantiated objects of the specified class.
	 */
	
	protected initiation( data : TData ) {
		const properties = [
			...this.properties(),
			...this.relationships(),
		];
		const temp = Object.fromEntries(
			Object.entries( data as Object )
			      .filter(
				      ( [ key, value ] ) => value !== null && value !== undefined &&
				                            properties.includes( key as keyof TData ),
			      )
		) as TData;
		Object.assign( this, temp );
	}
	
	/**
	 * Method for return the properties of the object that are fillable on init
	 * @returns  An array with the keys of the properties.
	 */
	protected abstract properties() : Array<keyof TData>;
	
	/**
	 * Method for return the relationships of the object that are fillable on init
	 * @returns  An array with the keys of the relationships.
	 */
	protected abstract relationships() : Array<keyof TData>;
	
	/*initRelatedArray<T extends BaseModel<any, any>>( data: Array<any>  ,
	                                       ClassRef : new (data) => T ) : PropertyType<Array<PropertyType<T>>> {
		return Array.isArray( data )
		       ? data.map( item => new ClassRef( item ) )
		       : undefined;
	}*/
	protected initRelatedArray<T extends BaseModel>(
		data : null | Array<ConstructorParameters<new ( data : any ) => T>[0]>,
		ClassRef : new ( data : ConstructorParameters<new ( data : any ) => T>[0] ) => T,
	) : PropertyType<Array<PropertyType<T>>> {
		return Array.isArray( data )
		       ? data.map( ( item ) => new ClassRef( item ) ) as PropertyType<T>[]
		       : null;
	}
	
	/**
	 * Initialize a related object if data exists.
	 *
	 * @param data - The data to initialize the object with.
	 * @param ClassRef - The class to instantiate.A class extending BaseModel
	 * @returns The initialized object or null.
	 */
	protected initRelatedObject<T extends BaseModel<any, any>>(
		data : ConstructorParameters<new ( data : any ) => T>[0] | null,
		ClassRef : new ( data : ConstructorParameters<new ( data : any ) => T>[0] ) => T,
	) : PropertyType<T> {
		return data
		       ? new ClassRef( data )
		       : null;
	}
	
	/**
	 * Safely convert a value to a Boolean.
	 * @param  value - The value to convert.
	 * @returns The converted Boolean or null if invalid.
	 */
	protected initToBoolean( value : any ) : boolean {
		
		if ( value !== null && value !== undefined ) return Boolean( value );
		throw ( new InvalidModelDataError( { type : "boolean" } ) );
	}
	
	/**
	 * Safely convert a value to a Date.
	 * @param value - The value to convert.
	 * @returns The converted Date or null if invalid.
	 */
	protected initToDate( value : any ) : Date {
		const date = new Date( value );
		if ( !isNaN( date.getTime() ) ) return date;
		throw ( new InvalidModelDataError( { type : "date" } ) );
	}
	
	/**
	 * Safely convert a value to an instance of the given enum class.
	 * The enum class must extend BaseEnum and have a static `findByValue` method.
	 *
	 * @param enumClass - The enum class extending BaseEnum.
	 * @param value - The value to convert.
	 * @returns An instance of the enum class or null if the value is invalid
	 */
	protected initToEnum<E extends typeof BaseEnum>( enumClass : E, value : any ) : InstanceType<E> | null {
		if ( value instanceof enumClass ) {
			value = value.value;
		}
		const enumI = enumClass.findByValue( value );
		if ( enumI ) return enumI;
		throw ( new InvalidModelDataError( { type : enumClass.name } ) );
	}
	
	/**
	 * Safely convert a value to a number.
	 * @param {*} value - The value to convert.
	 * @returns {number | null} The converted number or null if invalid.
	 */
	protected initToNumber( value : any ) : number | null {
		if ( !isNaN( value ) ) return Number( value );
		throw ( new InvalidModelDataError( { type : "number" } ) );
	}

	
}

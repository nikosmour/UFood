import { DevelopmentError } from "@/errors/DevelopmentError";

type PartialOfT<T> = Partial<Record<keyof T, any>>;
type ChangedProperties<T> = PartialOfT<T>;

/**
 * Base class for models in the application.
 * Provides utility methods and setup for Axios and Ziggy routes.
 */
export default abstract class ChangeTracker {
	[ key : string ] : any;
	
	/**
	 * Flag to determine whether an error should be thrown if an untracked property is accessed.
	 * When set to error, attempting to access an untracked property will throw an error.
	 * When set to console , a console error will show.
	 */
	ToThrowErrorIfItsNotBeTracked : "error" | "console" | false = "error";
	/**
	 * Stores the changed properties of the model.
	 * Each key corresponds to a property of the model, and its value represents the modified data.
	 */
	#changedProperties : ChangedProperties<this> = {};
	/**
	 * Stores the initial values of the model properties.
	 * This is used to compare with the current values to track changes.
	 */
	#initialValues : PartialOfT<this> = {};
	
	protected constructor() {
		this.#bindAllMethods();
		return this.getProxy();
	}
	
	/**
	 * Retrieves the tracked changes on the model.
	 * Changes represent the differences between the current values and the original values.
	 *
	 * @returns {ChangedProperties<this>} An object containing the tracked changes.
	 */
	get dirty() : ChangedProperties<this> {
		return this.#changedProperties;
	}
	
	/**
	 * Retrieves a list of all properties that have been modified from their initial values.
	 *
	 * @returns {Array<keyof this>} - An array of keys representing the changed properties.
	 * @example
	 * const instance = new MyModel();
	 * instance.someProp = "newValue";
	 * console.log(instance.getDirty()); // ["someProp"]
	 */
	get DirtyKeys() : Array<keyof this> {
		return Object.keys( this.#changedProperties ) as Array<keyof this>;
	}
	
	/**
	 * Synchronize the current values as the original state for all or specific attributes.
	 * This resets the tracked changes for those attributes.
	 *
	 * @param {Array<keyof this> | null} attributes - An array of attributes to sync, or null to sync all.
	 */
	syncCurrent( attributes : Array<keyof this> | null = null ) : void {
		if ( attributes === null ) {
			// Sync all attributes
			this.#initialValues = this.defineInitialValues();
			Object.keys( this.#changedProperties )
			      .forEach( ( key ) => delete this.#changedProperties[ key ] );
		} else {
			// Sync specified attributes
			for ( const key of attributes ) {
				if ( this.#throwErrorIfItsNotBeTracked( key ) ) {
					this.#initialValues[ key ] = this[ key ];
					delete this.#changedProperties[ key ];
				}
			}
		}
	}
	
	/**
	 * Check if the model or specific attributes have unsaved changes.
	 *
	 * @param {Array<keyof this> | keyof this | null} attributes - A specific attribute (string), an array of attributes, or null to check all.
	 * @returns {boolean} True if the model or the specified attributes are dirty, otherwise false.
	 */
	isDirty( attributes : Array<keyof this> | keyof this | null = null ) : boolean | null {
		if ( attributes === null ) {
			return Object.keys( this.#changedProperties ).length > 0;
		}
		if ( typeof attributes === "string" ) {
			return ( this.#throwErrorIfItsNotBeTracked( attributes ) )
			       ? attributes in this.#changedProperties
			       : null;
		}
		return ( attributes as Array<keyof this> ).some( ( attr ) => this.isDirty( attr ) );
	}
	
	/**
	 * Check if the model or specific attributes have no unsaved changes.
	 *
	 * @param {Array<keyof this> | keyof this | null} attributes - A specific attribute (string), an array of attributes, or null to check all.
	 * @returns {boolean} True if the model or the specified attributes are clean, otherwise false.
	 */
	isClean( attributes : Array<keyof this> | keyof this | null = null ) : boolean | null {
		return !this.isDirty( attributes );
	}
	
	/**
	 * Resets a specific property to its initial value and removes it from the list of changes.
	 *
	 * @param {keyof this} prop - The property to reset.
	 * @example
	 * const instance = new MyModel();
	 * instance.someProp = "newValue";
	 * console.log(instance.isDirty("someProp")); // true
	 * instance.resetProperty("someProp");
	 * console.log(instance.isDirty("someProp")); // false
	 * console.log(instance.someProp); // Original value
	 */
	resetProperty( prop : keyof this ) : void {
		this.#throwErrorIfItsNotBeTracked( prop );
		if ( this.isDirty( prop ) ) {
			this[ prop ] = this.#initialValues[ prop ];
			delete this.#changedProperties[ prop ];
		}
	}
	
	/**
	 * Retrieves the original value of a property (before any changes were made).
	 *
	 * @param {keyof this} prop - The property whose original value is to be fetched.
	 * @returns {any} - The original value of the specified property.
	 * @example
	 * const instance = new MyModel();
	 * console.log(instance.getOriginal("someProp")); // Original value of someProp
	 */
	getOriginal( prop : keyof this ) : any {
		this.#throwErrorIfItsNotBeTracked( prop );
		return this.#initialValues[ prop ];
	}
	
	syncOriginal() : void {
		// Iterate over the keys in #initialValues and update the instance properties
		Object.keys( this.#initialValues )
		      .forEach( ( prop : keyof this ) => {
			      // Cast the key as a valid property of the instance
			      this.resetProperty( prop );
		      } );
	}
	
	/**
	 * Get a proxy that tracks changes to the object's properties.
	 */
	public getProxy<T extends this>() : this {
		return new Proxy( this, {
			get : ( target : T, prop : keyof T ) => {
				return target[ prop ];
			},
			set : ( target : T, prop : keyof T, value ) => {
				if ( target.shouldBeTracked( prop ) ) {
					if ( target.#initialValues[ prop ] !== value ) {
						target.#changedProperties[ prop ] = value;
					} else {
						delete target.#changedProperties[ prop ]; // Reset change
					}
				}
				target[ prop ] = value;
				return true;
			},
		} );
	}
	
	public trackableProps<T extends this>() : Array<keyof T> {
		return Object.keys( this ) as ( keyof T )[];
	}
	
	/**
	 * Define the initial values for tracking.
	 */
	protected defineInitialValues<T extends this>() : Partial<T> {
		const values : Partial<T> = {};
		for ( const key of this.trackableProps() ) {
			values[ key ] = this[ key ];
		}
		return values;
	}
	
	/**
	 * Determine whether a property should be tracked for changes.
	 */
	protected shouldBeTracked<T extends this>( prop : keyof T ) : boolean {
		return this.trackableProps()
		           .includes( prop as string );
	}
	
	/**
	 * Bind all methods of the class to the current instance.
	 */
	#bindAllMethods<T extends this>() {
		let prototype = Object.getPrototypeOf( this );
		
		while ( prototype && prototype !== Object.prototype ) {
			const propertyNames = Object.getOwnPropertyNames( prototype ) as Array<keyof T>;
			
			for ( const prop of propertyNames ) {
				if ( prop === "constructor" ) continue;
				
				const descriptor = Object.getOwnPropertyDescriptor( prototype, prop );
				if ( !descriptor ) continue;
				
				if ( typeof descriptor.value === "function" ) {
					this[ prop ] = this[ prop ].bind( this );
				} else {
					Object.defineProperty( this, prop, {
						get :          descriptor.get
						               ? descriptor.get.bind( this )
						               : undefined,
						set :          descriptor.set
						               ? descriptor.set.bind( this )
						               : undefined,
						enumerable :   descriptor.enumerable,
						configurable : descriptor.configurable,
					} );
				}
			}
			
			prototype = Object.getPrototypeOf( prototype );
		}
	}
	
	#throwErrorIfItsNotBeTracked<T extends this>( prop : keyof T ) {
		if ( this.shouldBeTracked( prop ) )
			return true;
		if ( this.ToThrowErrorIfItsNotBeTracked === "error" )
			throw new DevelopmentError( {
				                            message : "You are attempting to retrieve the original value of a model property that is not being tracked.",
			                            } );
		if ( this.ToThrowErrorIfItsNotBeTracked === "console" )
			console.error(
				"You are attempting to retrieve the original value of a model property that is not being tracked." );
		return false;
	}
}

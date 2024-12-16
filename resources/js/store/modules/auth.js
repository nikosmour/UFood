/**
 * @typedef {Object} UserData
 * @property {number} id - The unique identifier for the user.
 * @property {string} name - The user's full name.
 * @property {string} email - The user's email address.
 * @property {EnumUnit} status - The user's role (e.g., "phd", "staff entry", "staff coupon").
 * @property {Array<EnumUnit>} abilities - List of abilities assigned to the user.
 * @property {number|null} couponCategory
 * Only for Academic users; --
 * @property {boolean} [is_active] - indicates if the user is active.
 * @property {number} [a_m] - Unique student number
 * @property {number} [academic_id] - Unique academic ID
 * @property {CouponOwner}  [couponOwner]
 */
/**
 * @typedef {Object} CouponOwner
 * @property {number} academic_id
 * @property {number} breakfast
 * @property {number} lunch
 * @property {number} dinner
 */

import { getModelClass } from "@utilities/modelUserMapper";
import { UserAbilityEnum } from "@enums/UserAbilityEnum";
import AxiosInstance from "@/plugins/axios";

/**
 * Vuex state for authentication.
 * @type {{ isLoggedIn: boolean, user: UserData | null }}
 */
export const state = {
	user : null,
};

export const mutations = {
	/**
	 * Sets the logged-in user and initializes their enums.
	 * @param state
	 * @param {Object} payload - The payload containing user data.
	 */
	setLogin( state, payload ) {
		const ModelClass = getModelClass( payload.model ); // Pass the model name from the backend
		payload.user.abilities = UserAbilityEnum.findByValueMany( payload.user.abilities );
		const user = ModelClass
		             ? new ModelClass( payload.user )
		             : payload.user;
		state.user = user;
		
	},
	
	/**
	 * Logs out the user.
	 * @param state
	 */
	setLogout( state ) {
		state.user = null;
	},
};

export const actions = {
	/**
	 * Handles user login.
	 * @param dispatch
	 * @param credentials
	 * @returns {Promise<boolean|null>}
	 */
	async loginUser( { dispatch }, credentials ) {
		// Send login request to Laravel backend
		return await AxiosInstance.post( "api/login", credentials )
		                  .then( response => {
			                  console.log( "status", response );
			                  return ( response.status === 204 )
			                         ? dispatch( "getUser" )
			                         : ( response.status === 200 )
			                           ? dispatch( "getUser", response )
			                           : null;
		                  } );
		
	},
	
	/**
	 * Fetches the currently logged-in user.
	 * @param commit
	 * @param responseLogin if it call after user Login.
	 * @throws error
	 * @returns {Promise<boolean>}
	 */
	async getUser( { commit }, responseLogin ) {
		try {
			const { data } = responseLogin || await AxiosInstance.get( "/api/user" );
			
			if ( data ) {
				localStorage.setItem( "user", JSON.stringify( data.user ) ); // Store user data
				await commit( "setLogin", data );
				return true;
			}
		} catch ( error ) {
			throw error;
		}
		localStorage.removeItem( "user" ); // Store user data
		commit( "setLogout" );
		return false;
	},
	
	/**
	 * Logs out the user.
	 * @param commit
	 * @returns {Promise<void>}
	 */
	async logout( { commit } ) {
		await AxiosInstance.post( "api/logout" );
		localStorage.removeItem( "user" );
		commit( "setLogout" );
	},
};

export const getters = {
	/**
	 * Checks if a user is authenticated.
	 * @param state
	 * @returns {boolean}
	 */
	isAuthenticated( state ) {
		console.log( "auth/isAuthenticated", state );
		return state.user != null;
	},
	
	/**
	 * Returns the current user.
	 * @param state
	 * @returns {?UserData}
	 */
	currentUser( state ) {
		return state.user;
	},
	
	/**
	 * Checks if a user has a specific ability.
	 * @param state
	 * @returns {function(EnumUnit): boolean}
	 */
	hasAbility( state ) {
		return ( ability ) => {
			console.log( "auth/hasAbility", ability );
			return state.user !== null && state.user.abilities.includes( ability );
		};
	},
	/**
	 * Checks if a user has a specific ability.
	 * @param state
	 * @returns boolean
	 */
	isNew( state ) {
		return state.user !== null && state.user.abilities.length === 0;
	},
	/**
	 * Get the card status of the user.
	 * @param {Object} state
	 * @returns {EnumUnit | null}
	 */
	cardStatus : ( state ) => {
		return (
			state.user?.card_applicant?.current_card_application?.card_last_update?.status ||
			null
		);
	},
	
	/**
	 * Get the expiration date of the user's valid card application.
	 * @param {Object} state
	 * @returns {Date | null}
	 */
	cardExpirationDate : ( state ) => {
		return (
			state.user?.card_applicant?.valid_card_application?.expiration_date || null
		);
	},
	cardApplication : ( state ) => {
		return state.user?.card_applicant?.current_card_application; 
	},
};

export default {
	namespaced : true, // Use namespacing for modularity
	state,
	mutations,
	actions,
	getters,
};

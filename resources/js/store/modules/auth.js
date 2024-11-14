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
import { UserAbilityEnum } from "../../enums/UserAbilityEnum.js";
import { UserStatusEnum } from "../../enums/UserStatusEnum.js";
import { CardStatusEnum } from "../../enums/CardStatusEnum.js";

/**
 * Vuex state for authentication.
 * @type {{ isLoggedIn: boolean, user: UserData | null }}
 */
export const state = {
        user: null,
};

export const mutations = {
    /**
     * Sets the logged-in user and initializes their enums.
     * @param state
     * @param {Object} payload - The payload containing user data.
     */
    setLogin(state, payload) {
        const user = { ...payload.user };
        
        // Initialize enums
        user.abilities = UserAbilityEnum.findByValueMany( user.abilities );
        user.status = UserStatusEnum.findByValue( user.status );
        
        const cardLastUpdate = user.card_applicant?.current_card_application?.card_last_update;
        if ( cardLastUpdate ) {
            cardLastUpdate.status = CardStatusEnum.findByValue( cardLastUpdate.status );
        }
        state.user =/** @type {UserData} */ user;
    },
    
    /**
     * Logs out the user.
     * @param state
     */
    setLogout(state) {
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
        return await axios.post('api/login', credentials).then(response => {
            console.log('status', response)
            return (response.status === 204) ? dispatch("getUser") : (response.status === 200) ? response.data : null;
        });

    },
    
    /**
     * Fetches the currently logged-in user.
     * @param commit
     * @returns {Promise<boolean>}
     */
    async getUser( { commit } ) {
        
        const { data } = await axios.get( "/api/user" );

        if (data) {
            localStorage.setItem('user', JSON.stringify(data.user)); // Store user data
            await commit('setLogin', data);
            return true;
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
        await axios.post('/logout');
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
    isAuthenticated(state) {
        console.log('auth/isAuthenticated', state);
        return state.user != null;
    },
    
    /**
     * Returns the current user.
     * @param state
     * @returns {?UserData}
     */
    currentUser(state) {
        return state.user;
    },
    
    /**
     * Checks if a user has a specific ability.
     * @param state
     * @returns {function(EnumUnit): boolean}
     */
    hasAbility(state) {
        return (ability) => {
            console.log('auth/hasAbility', ability);
            return state.user !== null && state.user.abilities.includes(ability);
        };
    },
};

export default {
    namespaced: true, // Use namespacing for modularity
    state,
    mutations,
    actions,
    getters,
};

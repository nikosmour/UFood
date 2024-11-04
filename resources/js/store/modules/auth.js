/**
 * @typedef {Object} UserData
 * @property {number} id - The unique identifier for the user.
 * @property {string} name - The user's full name.
 * @property {string} email - The user's email address.
 * @property {string} status - The user's role (e.g., "phd", "staff entry", "staff coupon").
 * @property {Array<string>} abilities - List of abilities assigned to the user.
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
/**
 * @type {{ isLoggedIn: boolean, user: UserData | null }}
 */
export const state = {
        isLoggedIn: false,
        user: null,
};

export const mutations = {
    setLogin(state, payload) {
        state.user =/** @type {UserData} */  payload.user;
    },
    setLogout(state) {
        state.user = null;
    },
};

export const actions = {
    async loginUser({dispatch}, credentials) {
        // Send login request to Laravel backend
        return await axios.post('api/login', credentials).then(response => {
            console.log('status', response)
            return (response.status === 204) ? dispatch("getUser") : (response.status === 200) ? response.data : null;
        });

    },
    async getUser({commit}) {
        // Send login request to Laravel backend

        const data = await axios.get('/api/user').then(response => {
            return response.data;
        }).catch(error => {
            if (error.response.status === 401)
                return null;

        });
        console.log(data);

        if (data) {
            await commit('setLogin', data);
            localStorage.setItem('user', JSON.stringify(data.user)); // Store user data
            return true;
        } else {
            localStorage.removeItem('user'); // Store user data
            commit('setLogout');
            return false
        }
    },
    async logout({commit}) {
        await axios.post('/logout');
        localStorage.removeItem('user'); // Store user data
        commit('setLogout');
    },
};

export const getters = {
    /** @type {boolean} */
    isAuthenticated(state) {
        console.log('auth/isAuthenticated', state);
        return state.user != null;
    },
    /** @type {?UserData} */
    currentUser(state) {
        return state.user;
    },
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


export const state = {
        isLoggedIn: false,
        user: null,
    abilities: null,
};

export const mutations = {
    setLogin(state, payload) {
        state.user = payload.user;
        state.abilities = payload.abilities;
    },
    setLogout(state) {
        state.user = null;
        state.abilities = null;
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
    async getUser({state, commit}) {
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
            // dispach('logout');
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
    isAuthenticated(state) {
        console.log('auth/isAuthenticated', state);
        return state.user != null;
    },
    currentUser(state) {
        return state.user;
    },
    hasAbility(state) {
        return (ability) => {
            console.log('auth/hasAbility', state);
            return state.user && state.abilities.includes(ability);
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

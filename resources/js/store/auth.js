import Vuex from 'vuex';

export const state = {
    auth: { // Add the auth property here
        isLoggedIn: false,
        user: null,
    },
};

export const mutations = {
    setLogin(state, payload) {
        state.auth.user = payload.user;
    },
    setLogout(state) {
        state.auth.user = null;
    },
};

export const actions = {
    async loginUser({commit}, credentials) {
        // Send login request to Laravel backend
        const response = await axios.post('api/login', credentials).then(response => {
            return response;
        });
        console.log(response, response.data);

        if (response.data.success) {
            commit('setLogin', {user: response.data.user});
            localStorage.setItem('user', JSON.stringify(response.data.user)); // Store user data
            return true;
        } else {
            // Handle login failure
            return false
        }
    },
    async getUser({state, commit}) {
        // Send login request to Laravel backend

        const user = await axios.get('/api/user').then(response => {
            return response.data;
        }).catch(error => {
            if (error.response.status = 401)
                return null;

        });
        console.log(user);

        if (user) {
            await commit('setLogin', {user: user});
            localStorage.setItem('user', JSON.stringify(user)); // Store user data
            return true;
        } else {
            // dispach('logout');
            localStorage.removeItem('user'); // Store user data
            commit('setLogout');
            return false
        }
    },
    async logout({commit}) {
        await axios.post(route('logout'));
        localStorage.removeItem('user'); // Store user data
        commit('setLogout');
    },
};

export const getters = {
    isAuthenticated(state) {
        return state.auth.user != null;
    },
    currentUser(state) {
        return state.auth.user;
    },

};
export default new Vuex.Store({
    state,
    mutations,
    actions,
    getters,
});

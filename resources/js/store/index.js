import { createStore } from 'vuex';

// Create Vuex store instance
const store = createStore({
    state: {
        user: null,
    },
    mutations: {
        setUser(state, user) {
            state.user = user;
        }
    },
    actions: {
        authenticate({ commit }, user) {
            commit('setUser', user);
        }
    }
});

export default store;

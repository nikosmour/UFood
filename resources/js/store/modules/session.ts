// Define the type for the state
import type { AxiosResponse } from "axios";
import type { ActionTree, GetterTree, Module, MutationTree } from "vuex";

export interface State {
	timeLeft : number;
}

// Vuex state
export const state : State = {
	timeLeft : Number( import.meta.env.VITE_SESSION_TIME_OUT ) * 60 - 5,
};

// Vuex mutations
export const mutations : MutationTree<State> = {
	/**
	 * Sets the timeLeft value in the state.
	 */
	setTimeLeft( state, timeLeft : number ) {
		state.timeLeft = timeLeft;
	},
};

// Vuex actions
export const actions : ActionTree<any, any> = {
	updateTimeLeft( { commit }, timeLeft ) {
		commit( "setTimeLeft", timeLeft ?? Number( import.meta.env.VITE_SESSION_TIME_OUT ) * 60 );
	},
	updateCookies( _, {
		route,
		axiosInstance,
	} ) : Promise<AxiosResponse<any, void>> {
		return axiosInstance.get( route( "isLogin" ) );
	},
};

// Vuex getters
export const getters : GetterTree<State, number> = {
	/**
	 * Returns the current timeLeft value from the state.
	 */
	getTimeLeft( state ) {
		return state.timeLeft;
	},
};

export default {
	namespaced : true, // Use namespacing for modularity
	state,
	mutations,
	actions,
	getters,
} as Module<any, any>;

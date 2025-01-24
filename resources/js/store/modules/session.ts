// Define the type for the state
import type { AxiosResponse } from "axios";
import type { ActionTree, GetterTree, Module, MutationTree } from "vuex";

export interface State {
	timeLeft : number;
	refreshTries : number | null;
}

// Vuex state
export const state : State = {
	timeLeft : Number( import.meta.env.VITE_SESSION_TIME_OUT ) * 60 - 3,
	refreshTries : null,
};

// Vuex mutations
export const mutations : MutationTree<State> = {
	/**
	 * Sets the timeLeft value in the state.
	 */
	setTimeLeft( state, timeLeft : number ) {
		state.timeLeft = timeLeft;
	},
	setRefreshTries( state ) {
		state.refreshTries = window.setInterval( () => {
			clearInterval( state.refreshTries as number );
			state.refreshTries = null;
		}, 5000 );
	},
};

// Vuex actions
export const actions : ActionTree<any, any> = {
	updateTimeLeft( { commit }, timeLeft ) {
		commit( "setTimeLeft", timeLeft ?? Number( import.meta.env.VITE_SESSION_TIME_OUT ) * 60 - 3 );
	},
	updateCookies( {
		               commit,
		               getters,
	               }, {
		route,
		axiosInstance,
	               } ) : Promise<AxiosResponse<any, boolean>> | boolean {
		if ( getters.getRefreshTries ) {
			throw Error( "refresh error" );
		}
		commit( "setRefreshTries" );
		return axiosInstance.post( route( "isLogin" ) );
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
	getRefreshTries( state ) {
		console.info( state.refreshTries );
		return state.refreshTries;
	},
};

export default {
	namespaced : true, // Use namespacing for modularity
	state,
	mutations,
	actions,
	getters,
} as Module<any, any>;

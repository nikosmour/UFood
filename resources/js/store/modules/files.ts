/**
 * Vuex state for authentication.
 * @type {{ isLoggedIn: boolean, user: UserData | null }}
 */
export const state = {
	url : null,
};

export const mutations = {
	/**
	 * Sets the url that you need to show
	 * @param state
	 * @param {string} url - The url for the file to show.
	 */
	setPreviewUrl( state, url ) {
		
		state.url = url;
		
	},
	
};

export const actions = {};

export const getters = {
	/**
	 * Take the url for show
	 * @param state
	 * @returns {boolean}
	 */
	getPreviewUrl( state ) {
		console.log( "files/getPreviewUrl", state );
		return state.url;
	},
};

export default {
	namespaced : true, // Use namespacing for modularity
	state,
	mutations,
	actions,
	getters,
};

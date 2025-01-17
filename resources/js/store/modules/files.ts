/**
 * Vuex state for authentication.
 * @type {{ isLoggedIn: boolean, user: UserData | null }}
 */
export const state = {
	url : null,
	file : null,
};

export const mutations = {
	/**
	 * Sets the url that you need to show
	 * @param state
	 // * @param {string} url - The url for the file to show.
	 */
	setPreviewUrl( state, target ) {
		state.url = target?.url || null;
		state.file = target?.file;
		
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
		console.info( "files/getPreviewUrl" );
		return state.url;
	},
	getPreviewFile( state ) {
		console.info( state.file );
		return state.file;
	},
};

export default {
	namespaced : true, // Use namespacing for modularity
	state,
	mutations,
	actions,
	getters,
};

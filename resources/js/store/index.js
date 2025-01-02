import Vuex from "vuex";
import auth from "./modules/auth"; // Import the auth module
import files from "./modules/files";
import session from "./modules/session";

export default new Vuex.Store( {
	                               modules : {
		                               auth, // Register the auth module under the 'auth' namespace
		                               files,
		                               session,
	                               },
                               } );

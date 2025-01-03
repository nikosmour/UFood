// authGuard.js
import store from "@store";

export default async ( to ) => {
	console.log( Date.now() % 10000, "AuthGuard" );
	
	// Check if user is already authenticated or try to get him
	let isAuth = store.getters[ "auth/isAuthenticated" ];
	let promise = ( isAuth || to.query.skipAuthCheck )
	              ? null
	              : store.dispatch( "auth/getUser" );
	// If the route requires authentication and the user is not authenticated, wait to check if a user will be fetched
	if ( ( to.meta.requiresAuth || to.meta.requiresAbility ) && promise ) {
		isAuth = await promise;//401 status has handle on auth.js
		
		// Redirect to log in if not authenticated and the route requires authentication
		// it must manage through app.vue why need it?
		if ( !isAuth )
			return {
				name :  "login",
				query : {
					skipAuthCheck : true,
					redirect :      to.fullPath,
				},
			};
	}
	// Check for specific abilities
	if ( to.meta.requiresAbility && !store.getters[ "auth/hasAbility" ]( to.meta.requiresAbility ) )
		return !( store.getters[ "auth/isNew" ] )
		       ? { name : "error.403" } // Redirect to an error page if unauthorized
		       : {
				name :  "userProfile",
				query : { redirect : to.fullPath },
			};
	
	// If all checks pass, continue to the requested route
};

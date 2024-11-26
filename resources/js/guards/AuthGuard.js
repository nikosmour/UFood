// authGuard.js
import store from "@store";

export default async ( to, from ) => {
	console.log( Date.now() % 10000, "AuthGuard" );
	
	// Check if user is already authenticated or try to get him
	let isAuth = store.getters[ "auth/isAuthenticated" ];
	let promise = null;
	if ( !isAuth )
		promise = store.dispatch( "auth/getUser" );
	
	// If the route requires authentication and the user is not authenticated, wait to check if a user will be fetched
	if ( ( to.meta.requiresAuth || to.meta.requiresAbility ) && promise ) {
		isAuth = await promise.then( response => {
			return response;
		} )
		                      .catch( error => {
			                      console.log( error );
			                      return false;
		                      } );
		
		// Redirect to log in if not authenticated and the route requires authentication
		// it must manage through app.vue why need it?
		if ( !isAuth )
			return {
				name :  "login",
				query : { redirect : to.fullPath },
			};
	}
	// Check for specific abilities
	if ( to.meta.requiresAbility && !store.getters[ "auth/hasAbility" ]( to.meta.requiresAbility ) )
		return {
			name : "error.403",  // Redirect to an error page if unauthorized
		};
	
	// If all checks pass, continue to the requested route
};

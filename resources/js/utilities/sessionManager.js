/**
 * Sets up session timeout management by periodically refreshing the CSRF token
 * and refreshing user data if authenticated.
 * @param {number} timeoutMin - Session timeout duration in minutes.
 * @param {import("axios").AxiosInstance} axios - The axios instance used for API calls.
 * @param {Object} vueInstance - The Vue component instance, used to call methods like `getUser`.
 */
export function setupSessionTimeout( timeoutMin, axios, vueInstance ) {
	const timeout = ( timeoutMin > 2 )
	                ? ( timeoutMin - 1 ) * 60000
	                : 60000;
	
	// CSRF token refresh interval
	setInterval( () => {
		axios.get( vueInstance.route( "sanctum.csrf-cookie" ) );
	}, timeout );
	
	// User data refresh interval if authenticated
	setInterval( () => {
		console.log( "User refresh interval" );
		if ( vueInstance.isAuthenticated ) {
			vueInstance.getUser();
		}
	}, timeout );
}

/**
 * Sets up axios interceptors to handle various response statuses like 419 (CSRF expired) and 401 (Unauthorized).
 * }
 */
export function setupAxiosInterceptor( {
	                                       $axios,
	                                       $store,
	                                       route,
	                                       $t,
	                                       $displayError,
                                       } ) {
	$axios.interceptors.response.use(
		response => {
			$store.dispatch( "session/updateTimeLeft" );
			return response;
		},
		error => {
			if ( !error.response ) return Promise.reject( error );
			
			const status = error.response.status;
			if ( status )
				$store.dispatch( "session/updateTimeLeft" );
			if ( status === 419 )
				return $store.dispatch( "session/updateCookies", {
					route,
					axiosInstance : $axios,
				} )
				             .then( () => $axios( error.config ) )
				            .catch( refreshError => {
					            console.error( "CSRF refresh error", refreshError );
					            return Promise.reject( error );
				            } );
			
			if ( status === 401 )
				$store.commit( "auth/setLogout" );
			else if ( status === 403 )
				$displayError( $t( "forbiddenAccess.details" ) );
			return Promise.reject( error );
		},
	);
}

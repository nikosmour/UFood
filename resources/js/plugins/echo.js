import Echo from "laravel-echo";

import Pusher from "pusher-js";
import { MockEcho } from "./mockEcho.js";

export const PusherInstance = Pusher;

let echoInstance = null;

/**
 * Initializes and returns the Echo instance if not already created.
 * This does not immediately connect to Pusher.
 *
 * @returns {Echo|MockEcho} Echo instance.
 */
function initializeEcho( $axios ) {
	if ( echoInstance ) return echoInstance;
	
	// Check if Echo is enabled in the environment, if not, use MockEcho
	if ( import.meta.env.VITE_ENABLE_ECHO !== "true" ) {
		console.warn( "Using MockEcho. Echo is disabled for this environment." );
		echoInstance = new MockEcho();
		return echoInstance;
	}
	
	// Initialize the real Echo instance
	try {
		echoInstance = new Echo( {
			                         broadcaster : "pusher",
			                         key : import.meta.env.VITE_PUSHER_APP_KEY,
			                         cluster : import.meta.env.VITE_PUSHER_APP_CLUSTER,
			                         forceTLS : true,
			                         enabledTransports : [
				                         "ws",
				                         "wss",
			                         ], // WebSocket only
			                         disableStats : true, // Optional: Disable pusher's stats collection
		                         } );
		$axios.interceptors.request.use(
			( config ) => {
				// Retrieve the socket ID from Laravel Echo
				const socketId = echoInstance.socketId();
				
				// Attach the X-Socket-ID header to the request
				if ( socketId ) {
					config.headers = {
						...config.headers,
						"X-Socket-ID" : socketId,
					};
				}
				
				return config;
			},
			( error ) => {
				return Promise.reject( error );
			},
		);
	} catch ( error ) {
		console.warn( "Using MockEcho. Error initializing Echo:", error.message );
		echoInstance = new MockEcho();
	}
	
	return echoInstance;
}

/**
 * Connects to Pusher by initializing the Echo instance.
 * Returns the connected Echo instance.
 */
export function connectEcho( $axios ) {
	const echo = initializeEcho( $axios );
	
	// If using MockEcho, skip connection
	if ( echo instanceof MockEcho ) {
		console.warn( "MockEcho in use. Connection skipped." );
		return echo;
	}
	
	if ( !echo.connector.pusher.connection.state === "connected" ) {
		echo.connector.pusher.connect();
		console.log( "Pusher connected." );
	}
	
	return echo;
}

/**
 * Disconnects from Pusher, releasing resources.
 */
export function disconnectEcho() {
	if ( echoInstance && !( echoInstance instanceof MockEcho ) ) {
		echoInstance.connector.pusher.disconnect();
		console.log( "Pusher disconnected." );
	}
}

/**
 * Returns the current Echo instance without connecting or disconnecting.
 * Useful for accessing methods like `channel` or `listen` without immediate connection.
 */
export function getEcho( $axios ) {
	return initializeEcho( $axios );
}
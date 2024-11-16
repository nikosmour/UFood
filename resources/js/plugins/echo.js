import Echo from "laravel-echo";

import Pusher from "pusher-js";
import { MockEcho } from "./mockEcho.js";

export const PusherInstance = Pusher;

/**
 * Echo instance used for broadcasting events in a real-time application.
 * Conditionally initializes Echo based on the environment setting in the `.env` file.
 *
 * If Echo is disabled (via `VITE_ENABLE_ECHO=false`) or no internet connection, a mock Echo instance is used instead.
 *
 * @type {Echo|MockEcho} Echo instance or MockEcho instance based on the environment configuration.
 */
function getEcho() {
	// Check if Echo is enabled in the environment, if not, use MockEcho
	if ( import.meta.env.VITE_ENABLE_ECHO !== "true" ) {
		console.warn( " Using MockEcho. Echo is disabled for this environment." );
		return new MockEcho();
	}
	if ( !navigator.onLine ) {
		console.warn( " Using MockEcho. No internet connection." );
		return new MockEcho();
	}
	
	try {
		// Initialize the real Echo instance with Pusher configuration
		return new Echo( {
			                 broadcaster : "pusher",
			                 key :         import.meta.env.VITE_PUSHER_APP_KEY,
			                 cluster :     import.meta.env.VITE_PUSHER_APP_CLUSTER,
			                 forceTLS :    true,
		                 } );
	} catch ( error ) {
		// If there's an error during Echo initialization, fallback to MockEcho
		console.warn( " Using MockEcho. Error initializing Echo:", error.message );
	}
	return new MockEcho();
}

/**
 * Echo instance used for broadcasting events in a real-time application.
 * Conditionally initializes Echo based on the environment setting in the `.env` file.
 *
 * If Echo is disabled (via `VITE_ENABLE_ECHO=false`) or no internet connection, a mock Echo instance is used instead.
 *
 * @type {Echo|MockEcho} Echo instance or MockEcho instance based on the environment configuration.
 */
export const EchoInstance = getEcho();



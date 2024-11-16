export const ErrorManager = {
	install( app ) {
		// Create a global error handler
		app.config.errorHandler = ( error, vm, info ) => {
			console.error( `Error Captured by errorHandler Info ${ info } Error message: ${ error.message }` ); // Log the error to the console for debugging
		};
	},
};

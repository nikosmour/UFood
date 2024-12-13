import { ref } from "vue";
import type { AxiosError } from "axios";

const options = ref( {
	                     message : "",
	                     color : "error",
	                     showError : false,
	                     timeout : 5000,
                     } );
const notify = (
	{
		error,
		color = "info",
		timeout = 3000,
	} :
	{ error : string | AxiosError | Error, color : string, timeout : number } ) => {
	console.log( error, typeof error, typeof error === "string" );
	console.info( options.value, "options" );
	if ( typeof error === "string" )
		options.value[ "message" ] = error;
	else if ( error.response && error.response.data && error.response.data.message )
		options.value[ "message" ] = error.response.data.message;
	else if ( error.message )
		options.value[ "message" ] = error.message;
	else
		errorMessage.value = "An unexpected error occurred.";
	options.value.showError = true;
	options.value[ "color" ] = color ?? "info";
	options.value.timeout = timeout ?? 3000;
	console.info( options.value, "options" );
	
};
const notifyError = (
	{
		error,
		timeout = 3000,
	} :
	{ error : string | AxiosError | Error, color : string, timeout? : number } ) => {
	notify( {
		        error,
		        color : "error",
		        timeout,
	        } );
};


export const useNotifyHandler = {
	options,
	notify,
	notifyError,
};

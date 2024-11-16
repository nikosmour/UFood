import { Enums } from "./enums";
import { AxiosInstance } from "./axios";
import { EchoInstance } from "./echo";
import { capitalize, formatDate, formatNumber, truncate } from "./filters";
import { I18n } from "./i18n";
import { Vuetify } from "./vuetify";
import { Ziggy } from "./ziggy";
import { ZiggyVue } from "../../../vendor/tightenco/ziggy";
import { ErrorManager } from "./errorManager.js";


AxiosInstance.interceptors.request.use( config => {
	// Retrieve the socket ID from Laravel Echo
	const socketId = EchoInstance.socketId();
	
	// Attach the X-Socket-ID header to the request
	if ( socketId ) {
		config.headers[ "X-Socket-ID" ] = socketId;
	}
	
	return config;
}, error => {
	return Promise.reject( error );
} );
export const plugins = {
	install( app ) {
		app.config.globalProperties.$axios = AxiosInstance;
		// app.config.globalProperties.$pusher = PusherInstance;
		app.config.globalProperties.$echo = EchoInstance;
		app.config.globalProperties.$enums = Enums;
		app.config.globalProperties.$filters = {
			capitalize :   capitalize,
			truncate :     truncate,
			formatNumber : formatNumber,
			formatDate :   formatDate,
		};
		
		
		app.use( I18n );
		app.use( Vuetify );
		app.use( ErrorManager );
		
		app.use( ZiggyVue, Ziggy );
	},
};

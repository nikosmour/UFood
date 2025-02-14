import type { App, Plugin } from "vue"; // Import Vue's types
import { Enums } from "./enums";
import AxiosInstance from "./axios"; // Ensure AxiosInstance is correctly imported and typed
import { getEcho } from "./echo"; // Ensure EchoInstance is correctly typed
import { capitalize, formatDate, formatNumber, truncate } from "./filters";
import { I18n } from "./i18n";
import { Vuetify } from "./vuetify";
import { Ziggy } from "./ziggy";
import type { route } from "ziggy-js";
import { ZiggyVue } from "ziggy-js";
import { ErrorManager } from "./errorManager";
import BaseModel from "@utilities/BaseModel";
import { useNotifyHandler } from "@components/NotifyUser/NotifyPlugin";

Ziggy.url = import.meta.env.VITE_ZIGGY_BASE_URL ?? "";// to be valid in any url

// Define global property types
declare module "@vue/runtime-core" {
	export interface ComponentCustomProperties {
		$axios : typeof AxiosInstance;
		$echo : typeof EchoInstance;
		$enums : typeof Enums;
		$filters : {
			capitalize : typeof capitalize;
			truncate : typeof truncate;
			formatNumber : typeof formatNumber;
			formatDate : typeof formatDate;
		};
		route : typeof route; // Ensure `ZiggyVue` is typed correctly
	}
}

// Define the plugin
export const plugins : Plugin = {
	install( app : App ) {
		app.config.globalProperties.$axios = AxiosInstance;
		app.config.globalProperties.$echo = getEcho( AxiosInstance );//EchoInstance;
		app.config.globalProperties.$enums = Enums;
		app.config.globalProperties.$filters = {
			capitalize,
			truncate,
			formatNumber,
			formatDate,
		};
		
		app.use( I18n );
		app.use( Vuetify );
		app.use( ErrorManager );
		app.use( ZiggyVue, Ziggy );
		app.config.globalProperties.$notify = useNotifyHandler.notify;
		app.config.globalProperties.$displayError = useNotifyHandler.notifyError;
		
		// Set up BaseModel dependencies
		BaseModel.setup( app.config.globalProperties.route, AxiosInstance );
	},
};

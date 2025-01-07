import "vuetify/styles"; // Vuetify's styles
import { createVuetify } from "vuetify"; // Vuetify 3's createVuetify
import { aliases, mdi } from "vuetify/iconsets/mdi"; // Import iconset
import "@mdi/font/css/materialdesignicons.css"; // Import MDI icons
import { createVueI18nAdapter } from "vuetify/locale/adapters/vue-i18n";
import { useI18n } from "vue-i18n";
import { I18n } from "@/plugins/i18n";

function getPreferredTheme() {
	const prefersLight = window.matchMedia( "(prefers-color-scheme: light)" ).matches;
	return localStorage.getItem( "settings.theme" ) || prefersLight
	       ? "light"
	       : "dark";
}

// Create the Vuetify instance
export const Vuetify = createVuetify( {
	                                      theme :    {
		                                      defaultTheme : getPreferredTheme(),
	                                      },
	                                      icons :    {
		                                      defaultSet : "mdi",
		                                      aliases,
		                                      sets :       {
			                                      mdi,
		                                      },
	                                      },
	                                      defaults : {
		                                      VTextField : {
			                                      variant : "outlined",
		                                      },
		                                      VSelect :    {
			                                      variant : "outlined",
		                                      },
		                                      VCheckbox :  {
			                                      color : "primary",
		                                      },
	                                      },
	                                      locale : {
		                                      adapter : createVueI18nAdapter( {
			                                                                      i18n : I18n,
			                                                                      useI18n,
		                                                                      } ),
	                                      },
                                      } );

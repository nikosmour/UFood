import "vuetify/styles"; // Vuetify's styles
import { createVuetify } from "vuetify"; // Vuetify 3's createVuetify
import { aliases, mdi } from "vuetify/iconsets/mdi"; // Import iconset
import "@mdi/font/css/materialdesignicons.css"; // Import MDI icons

function getPreferredTheme() {
	const prefersLight = window.matchMedia( "(prefers-color-scheme: light)" ).matches;
	return prefersLight
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
		                                      VCheckbox :  {
			                                      color : "primary",
		                                      },
	                                      },
                                      } );

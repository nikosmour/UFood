import { createI18n } from "vue-i18n";
import messages from "@/locales"; //translations
// Function to determine the locale from browser; settings
const getPreferredLocale = () => {
	const browserLocales = navigator.languages || [ navigator.language ];
	for ( const locale of browserLocales ) {
		if ( [
			"en",
			"el",
		].includes( locale ) ) {
			return locale; // Return the first supported locale
		}
	}
	return "en"; // Default to English if no match
};

// Create the I18n instance
export const I18n = createI18n( {
	                                locale : localStorage.getItem( "settings.lang" ) || getPreferredLocale(), // set locale
	                                fallbackLocale : "en", // set fallback locale
	                                messages,
                                } );

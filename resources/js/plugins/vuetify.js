import 'vuetify/styles'; // Vuetify's styles
import {createVuetify} from 'vuetify'; // Vuetify 3's createVuetify
import {aliases, mdi} from 'vuetify/iconsets/mdi'; // Import iconset

// Create the Vuetify instance
export const Vuetify = createVuetify({
    icons: {
        defaultSet: 'mdi',
        aliases,
        sets: {
            mdi,
        },
    },
});

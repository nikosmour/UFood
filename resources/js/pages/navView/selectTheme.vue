// selectTheme.vue
<template>
    <v-select
        v-model = "themeCategory"
        :items = "themeOptions"
        :label = "$t('settings.theme.select')"
        variant = "outlined"
        @update:model-value = "updateTheme"
    ></v-select>
</template>

<script lang = "ts">
import { defineComponent } from "vue";

export default defineComponent( {
	name : "SelectTheme",

	data() {
		return {
			/**
			 * Selected theme category, initialized from local storage or defaulted to 'system'.
			 * @type {String}
			 */
			themeCategory : localStorage.getItem( "settings.theme" ) || "system",
		};
	},

	methods : {
		/**
		 * Determines the preferred lang based on the user's system settings.
		 * @returns `light` if the system preference is light mode, `dark` otherwise.
		 */
		getPreferredTheme() : string {
			return window.matchMedia( "(prefers-color-scheme: light)" ).matches
			       ? "light"
			       : "dark";
		},

		/**
		 * Updates the application theme based on the selected category.
		 * Saves the choice to local storage and sets Vuetify's global theme.
		 */
		updateTheme() {
			console.log( "Selected theme:", this.themeCategory );
			localStorage.setItem( "settings.theme", this.themeCategory );

			// Apply the selected theme, defaulting to system preference if set to 'system'
			this.$vuetify.theme.global.name = this.themeCategory === "system"
			                                  ? this.getPreferredTheme()
			                                  : this.themeCategory;
		},
	},
	                                computed : {
		                                /**
		                                 * List of theme options available for selection.
		                                 */
		                                themeOptions() : Array<{ value : string, title : string }> {
			                                return [
				                                "light",
				                                "dark",
				                                "system",
			                                ].map( ( theme ) => {
				                                return {
					                                value : theme,
					                                title : this.$t( "settings.theme." + theme ),
				                                };
			                                } );
		                                },
	                                },
                                } );
</script>

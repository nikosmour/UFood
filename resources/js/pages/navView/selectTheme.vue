// selectTheme.vue
<template>
    <v-select
        v-model = "themeCategory"
        :items = "themeOptions"
        :label = "$t('theme.select')"
        :aria-label = "$t('theme.select')"
        variant = "outlined"
        @update:model-value = "updateTheme"
    ></v-select>
</template>

<script>
export default {
	name : "SelectTheme",

	data() {
		return {
			/**
			 * Selected theme category, initialized from local storage or defaulted to 'system'.
			 * @type {String}
			 */
			themeCategory : localStorage.getItem( "settings.theme" ) || "system",

			/**
			 * List of theme options available for selection.
			 * @type {Array<Object>}
			 */
			themeOptions : [
				"light",
				"dark",
				"system",
			               ].map( ( theme ) => {
				return {
					value : theme,
					title : this.$t( "theme." + theme ),
				};
			} ),
		};
	},

	methods : {
		/**
		 * Determines the preferred theme based on the user's system settings.
		 * @returns {String} - 'light' if the system preference is light mode, 'dark' otherwise.
		 */
		getPreferredTheme() {
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

	created() {
		// Apply the theme on component creation
		this.updateTheme();
	},
};
</script>

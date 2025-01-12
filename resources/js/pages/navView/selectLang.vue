// selectTheme.vue
<template>
    <v-select
        v-model = "$i18n.locale "
        :items = "langOptions"
        :label = "$t('settings.lang.select')"
        variant = "outlined"
        @update:model-value = "updateLang"
    ></v-select>
</template>

<script lang = "ts">
import { defineComponent } from "vue";

export default defineComponent( {
	                                name : "UpdateLang",

	                                methods :  {

		                                /**
		                                 * Updates the application lang based on the selected category.
		                                 * Saves the choice to local storage and sets Vuetify's global lang.
		                                 */
		                                updateLang( newValue ) {
			                                this.$axios.defaults.headers[ "Accept-Language" ] = newValue;
			                                localStorage.setItem( "settings.lang", newValue );

		                                },
	                                },
	                                computed : {
		                                /**
		                                 * List of lang options available for selection.
		                                 * @type {Array<Object>}
		                                 */
		                                langOptions() {
			                                return [
				                                "en",
				                                "el",
			                                ].map( ( lang ) => {
				                                return {
					                                value : lang,
					                                title : this.$t( "settings.lang." + lang ),
				                                };
			                                } );
		                                },
	                                },
                                } );
</script>

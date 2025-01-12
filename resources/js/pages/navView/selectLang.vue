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
			                                if ( !( this.$i18n.messages[ newValue ]?.[ "backend" ] ) )
				                                this.getTranslation( newValue );
		                                },
		                                async getTranslation( lang = null ) {
			                                lang ??= this.$i18n.locale;
			                                try {
				                                const { data } = await this.$axios.get(
					                                this.route( "lang", { lang : lang } ) );
				                                this.addTranslations( lang, { "backend" : data } );
			                                } catch ( error ) {
				                                throw error;
			                                }
		                                },
		                                addTranslations( locale, translations ) {
			                                const i18n = this.$i18n;
			                                console.info( i18n );
			                                if ( !i18n.messages[ locale ] ) {
				                                i18n.messages[ locale ] = {};
			                                }
			                                i18n.setLocaleMessage( locale, {
				                                ...i18n.messages[ locale ],
				                                ...translations,
			                                } );
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

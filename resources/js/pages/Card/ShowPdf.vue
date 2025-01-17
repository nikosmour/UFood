<template>
    <v-card
        v-if = "docUrl" :loading = "loading"
        class = "pa-4 position-sticky h-screen" elevation = "1" style = "top:5em"
    >
        <object
            :data = "docUrl"
            class = "w-100 h-100"
            type = "application/pdf"
            @error = "onPdfError"
            @load = "onPdfLoad"
        />
    </v-card>
</template>
<script lang = "ts">
import { mapGetters } from "vuex";
import { defineComponent } from "vue";
// import VuetifyPdf from "vuetify-pdfjs/src/App.vue"

export default defineComponent( {
	name : "ShowPdf",
	                                components : [
		                                // VuetifyPdf
	                                ],
	data() {
		return {
			loading : false, // Initial loading state
		};
	},
	computed : {
		...mapGetters( "files", {
			docUrl : "getPreviewUrl",
		} ),
	},
	watch :    {
		// Watch for changes in the PDF URL
		docUrl( newUrl, oldUrl ) {
			console.log( `PDF URL changed from ${ oldUrl } to ${ newUrl }` );
			this.loading = !!newUrl; // Show the loading spinner again
		},
	},
	methods :  {
		onPdfLoad() {
			// Fired when the PDF finishes loading
			this.loading = false;
		},
		onPdfError() {
			// If there's an error loading the PDF
			this.loading = false;
			console.error( "Failed to load PDF" );
		},
	},
                                } );
</script>

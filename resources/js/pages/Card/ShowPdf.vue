<template>
    <v-sheet
        v-if = "docUrl"
        class = "pa-4 fill-height" elevation = "1"
    >
        <v-progress-circular
            v-if = "loading"
            color = "primary"
            indeterminate
            size = "50"
            style = "position: absolute; top: 50%; left: 50%; transform: translate(-50%, -50%);"
        />
        <object
            :data = "docUrl"
            class = "fill-height w-100"
            style = "min-height: 500px"
            type = "application/pdf"
            @error = "onPdfError"
            @load = "onPdfLoad"
        />
    </v-sheet>
</template>
<script lang = "ts">
import { mapGetters } from "vuex";

export default {
	name : "ShowPdf",
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
};
</script>
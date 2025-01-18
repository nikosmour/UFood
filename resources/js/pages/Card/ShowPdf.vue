<template>

    <v-card
        :loading = "loading"
        class = "pa-4 position-sticky h-screen " style = "top:5em ; height: 100vh ;"
    >
        <v-card-title class = "d-flex justify-space-between align-center">
            <v-list-item :title = "description" :subtitle = "file.file_name" />
            <v-btn-group>
                <v-btn
                    :aria-label = "$t('documents.magnify',overlay?1:0)" icon
                    @click = "updateoverlay"
                >
                    <v-icon>mdi-magnify-{{ overlay
                                           ? "close"
                                           : "plus" }}
                    </v-icon>
                </v-btn>

                <!-- Edit Button (Icon) -->
                <v-btn :aria-label = "$t('documents.close')" icon @click = "closeFile">
                    <v-icon :icon = "'mdi-close'"></v-icon>
                </v-btn>
            </v-btn-group>
        </v-card-title>
        <object
            :data = "docUrl"
            class = "w-100 h-screen"
            type = "application/pdf"
            @error = "onPdfError"
            @load = "onPdfLoad"
        />
    </v-card>
</template>
<script lang = "ts">
import { mapGetters, mapMutations } from "vuex";
import { defineComponent } from "vue";
// import VuetifyPdf from "vuetify-pdfjs/src/App.vue"

export default defineComponent( {
	name : "ShowPdf",
	                                components : [
		                                // VuetifyPdf
	                                ],
	                                emits : [
		                                "update:overlay",
	                                ],
	                                props : {
		                                overlay : {
			                                type :    Boolean,
			                                default : true,
		                                },
	                                },
	data() {
		return {
			loading : false, // Initial loading state
		};
	},
	computed : {
		...mapGetters( "files", {
			docUrl : "getPreviewUrl",
			file : "getPreviewFile",
		} ),
		description() : string | null {
			return this.$t( "backend.files." + this.file.description + ".short" );
		},
	},
	watch :    {
		// Watch for changes in the PDF URL
		docUrl( newUrl, oldUrl ) {
			console.info( `PDF URL changed from ${ oldUrl } to ${ newUrl }` );
			this.loading = !!newUrl; // Show the loading spinner again
		},
	},
	methods :  {
		...mapMutations( "files", [ "setPreviewUrl" ] ),
		onPdfLoad() {
			// Fired when the PDF finishes loading
			this.loading = false;
		},
		onPdfError() {
			// If there's an error loading the PDF
			this.loading = false;
			console.error( "Failed to load PDF" );
		},
		updateoverlay() {
			this.$emit( "update:overlay", !this.overlay );

		},
		closeFile() {
			this.setPreviewUrl( null );
		},
	},
	                                unmounted() {
	                                },
                                } );
</script>

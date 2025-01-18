<template>
    <v-row>
        <v-col :lg = "docUrl ? 6 : 12" cols = "12">
            <slot />
        </v-col>
        <!--        <VuetifyPdf v-if="docUrl" :url="docUrl"/>-->
        <v-col v-if = "docUrl" lg = "6">
            <ShowPdf ref = "ethue" v-model:overlay = "overlay" v-show = "!overlay" />
        </v-col>
    </v-row>
    <v-dialog v-if = "docUrl" v-model = "overlay">
        <ShowPdf ref = "ethue" v-model:overlay = "overlay" />
    </v-dialog>
</template>

<script lang = "ts">

import { mapGetters } from "vuex";
import ShowPdf from "@pages/Card/ShowPdf.vue";
// import VuetifyPdf from "vuetify-pdf-viewer/src/App.vue"
export default {
	name :       "CardApplicationAbstract",
	components : {
		ShowPdf,
	},
	data() {
		return {
			overlay : false,
		};
	},
	methods :  {},
	computed : {
		...mapGetters( "files", {
			docUrl : "getPreviewUrl",
		} ),
	},
	created() {
		this.overlay = this.$vuetify.display.mdAndDown;
	},
	watch : {
		"$vuetify.display.mdAndDown"( newValue ) {
			this.overlay = newValue;
		},
	},


};
</script>

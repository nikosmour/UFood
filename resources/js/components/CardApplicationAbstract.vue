<template>
    <v-row>
        <v-col :lg = "docUrl ? 6 : 12" cols = "12">
            <slot />
        </v-col>
        <!--        <VuetifyPdf v-if="docUrl" :url="docUrl"/>-->
        <v-col v-if = "docUrl" lg = "6">
            <ShowPdf />
        </v-col>
    </v-row>
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
		return {};
	},
	methods :  {},
	computed : {
		...mapGetters( "files", {
			docUrl : "getPreviewUrl",
		} ),
	},
	created() {
	},

};
</script>

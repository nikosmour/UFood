<template>
    <v-row>
        <v-col :lg = "docUrl ? 6 : 12" md = "12">
            <v-container max-width = "50rem">
        <MyFreeFoodStatus />
        <v-row justify = "center">
            <v-col cols = "12">
                <v-stepper
                    v-model = "step"
                    :items = "[$t('personalInfo'), $t('documents.upload'), $t('pendingReview')]"
                    hide-actions
                >
                    <template v-slot:item.1>
                        <CardApplicationCreateForm @created = "step=2" />
                    </template>

                    <template v-slot:item.2>
                        <DocumentEdit
                            :application = "application"
                            @submit = "step=3"
                        />
                    </template>
                    <template v-slot:item.3>
                        <ApplicationPreview
                            :application = "application"
                            @edit = "step=2"
                        />
                    </template>
                </v-stepper>
            </v-col>
        </v-row>
    </v-container>
        </v-col>
        <!--        <VuetifyPdf v-if="docUrl" :url="docUrl"/>-->
        <v-col lg = "6">
            <ShowPdf />
        </v-col>
    </v-row>
</template>

<script lang = "ts">

import { mapGetters } from "vuex";
import type CardApplication from "@models/CardApplication";
import ApplicationPreview from "@pages/Card/ApplicationPreview.vue";
import DocumentEdit from "@pages/Card/DocumentEdit.vue";
import CardApplicationCreateForm from "@pages/Card/CardApplicationCreateForm.vue";
import MyFreeFoodStatus from "@components/MyFreeFoodStatus.vue";
import ShowPdf from "@pages/Card/ShowPdf.vue";
// import VuetifyPdf from "vuetify-pdf-viewer/src/App.vue"
export default {
	name :       "CardApplication",
	components : {
		ShowPdf,
		MyFreeFoodStatus,
		CardApplicationCreateForm,
		DocumentEdit,
		ApplicationPreview,
		// VuetifyPdf
	},
	data() {
		return {
			isEditing : true,
			step :      0,
		};
	},
	computed : {
		...mapGetters( "auth", {
			application1 : "cardApplication",
		} ),
		...mapGetters( "files", {
			docUrl : "getPreviewUrl",
		} ),
		application() : CardApplication {
			return this.application1;
		},
	},
	created() {
		this.step = ( !this.application )
		            ?
		            1
		            : ( this.application.isEditing )
		              ?
		              2
		              : 3;
	},

};
</script>

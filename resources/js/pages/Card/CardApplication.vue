<template>
    <v-row>
        <v-col :lg = "docUrl ? 6 : 12" cols = "12">
            <v-container max-width = "50rem">
        <MyFreeFoodStatus />
                <v-alert
                    v-if = "untilDate"
                    :text = "$t('submit_application_until',{ 'date':untilDate.toLocaleDateString($i18n.locale)},canSubmit ? 1: 0)"
                    closable
                />
        <v-row justify = "center">
            <v-col cols = "12">
                <v-stepper
                    v-model = "step"
                    :items = "[$t('personalInfo'), $t('documents.upload'), $t('pendingReview')]"
                    hide-actions
                    :alt-labels = "alt_labels"
                    :mobile = "null"
                    mobile-breakpoint = "sm"
                    v-if = "canSubmit"
                >
                    <template v-slot:item.1>
                        <CardApplicationCreateForm @created = "moveStep2" />
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
                        />
                    </template>
                </v-stepper>
                <ApplicationPreview
                    v-else-if = "application "
                    :application = "application"
                    :isApplicationPeriodOpen = "false"
                />
            </v-col>
        </v-row>
    </v-container>
        </v-col>
        <!--        <VuetifyPdf v-if="docUrl" :url="docUrl"/>-->
        <v-col v-if = "docUrl" lg = "6">
            <ShowPdf />
        </v-col>
    </v-row>
</template>

<script lang = "ts">

import { mapActions, mapGetters } from "vuex";
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
			canSubmit : false,
			untilDate : null as Date,

		};
	},
	methods : {
		async moveStep2() {
			await this.getUser();
			this.step = 2;
		},
		...mapActions( "auth", [ "getUser" ] ),
	},
	computed : {
		...mapGetters( "auth", {
			application1 : "cardApplication",
			config : "config",
		} ),
		...mapGetters( "files", {
			docUrl : "getPreviewUrl",
		} ),
		application() : CardApplication {
			return this.application1;
		},
		alt_labels() : boolean {
			return this.$vuetify.display.smAndDown;
		},
		step() : number {
			const $t = this.application?.card_last_update?.status;
			return ( !this.application )
			       ? 1
			       : ( this.application.isEditing )
			         ? 2
			         : 3;
		},
	},
	created() {

		// Step 1: Check if lastDate is defined and valid
		const lastDate = this.config?.application?.lastDate;

// If lastDate is undefined, null, or not a valid date, set canSubmit to false
		if ( !lastDate || isNaN( ( new Date( lastDate ) ).getTime() ) ) {
			this.canSubmit = false;
			return;
		}
		// Step 2: Convert lastDate to a Date object
		const untilDate = new Date( lastDate );

		// Step 3: Get the current date and strip the time portion for comparison
		const currentDate = new Date();
		currentDate.setHours( 0, 0, 0, 0 ); // Set time to 00:00:00.000 to only compare the date part

		// Strip the time portion of untilDate as well
		untilDate.setHours( 0, 0, 0, 0 );

		// Step 4: Perform the comparison
		this.canSubmit = currentDate <= untilDate;
		if ( !this.application || this.application.canBeEdited )
			this.untilDate = untilDate;
	},

};
</script>

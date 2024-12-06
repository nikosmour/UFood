<template>
    <v-container max-width = "50rem">
        <v-row justify = "center">
            <v-col cols = "12">
                <v-stepper
                    v-model = "step"
                    :items = "[$t('personalInfo'), $t('documents.upload'), $t('pendingReview')]"
                    hide-actions
                >
                    <template v-slot:item.1>
                        <CardApplicationCreateForm />
                    </template>

                    <template v-slot:item.2>
                        <DocumentEdit
                            :application = "application"
                        />
                    </template>
                    <template v-slot:item.3>
                        <ApplicationPreview
                            :application = "application"
                        />
                    </template>
                </v-stepper>
            </v-col>
        </v-row>
    </v-container>
</template>

<script lang = "ts">

import { mapGetters } from "vuex";
import type CardApplication from "@models/CardApplication";
import ApplicationPreview from "@pages/Card/ApplicationPreview.vue";
import DocumentEdit from "@pages/Card/DocumentEdit.vue";
import CardApplicationCreateForm from "@pages/Card/CardApplicationCreateForm.vue";

export default {
	name :       "CardApplication",
	components : {
		CardApplicationCreateForm,
		DocumentEdit,
		ApplicationPreview,
	},
	computed : {
		...mapGetters( "auth", {
			application1 : "cardApplication",
		} ),
		application() : CardApplication {
			return this.application1;
		},
		step() : number {
			if ( !this.application )
				return 1;
			return ( this.application.isEditing )
			       ? 2
			       : 3;
		},
	},

};
</script>

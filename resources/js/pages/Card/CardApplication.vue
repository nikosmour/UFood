<template>
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
</template>

<script lang = "ts">

import { mapGetters } from "vuex";
import type CardApplication from "@models/CardApplication";
import ApplicationPreview from "@pages/Card/ApplicationPreview.vue";
import DocumentEdit from "@pages/Card/DocumentEdit.vue";
import CardApplicationCreateForm from "@pages/Card/CardApplicationCreateForm.vue";
import MyFreeFoodStatus from "@components/MyFreeFoodStatus.vue";

export default {
	name :       "CardApplication",
	components : {
		MyFreeFoodStatus,
		CardApplicationCreateForm,
		DocumentEdit,
		ApplicationPreview,
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

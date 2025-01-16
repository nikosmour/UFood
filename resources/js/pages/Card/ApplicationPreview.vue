<template>
    <v-card
        v-if = "application" :loading = "isLoading"
        class = "justify-content-around"
    >
        <v-card-text>
            <v-expansion-panels v-model = "panel" class = "mb-5" multiple>
                <v-expansion-panel :title = "$t('personalInfo')">
                    <v-expansion-panel-text>
                        <CardApplicantInfo :user = "currentUser" />
                    </v-expansion-panel-text>
                </v-expansion-panel>

                <v-expansion-panel :title = "$t('document',2)">
                    <v-expansion-panel-text>
        <MyCardApplicationFiles
            :application = "application"
            :loadings = "loadings"
            :isApplicationPeriodOpen = "canBeEditing"
        />
                    </v-expansion-panel-text>
                </v-expansion-panel>
            </v-expansion-panels>
        
        <v-textarea
            v-if = "comment"
            v-model = "comment"
            :label = "$t('comment.value')"
            auto-grow
            readonly
            variant = "plain"
        />
        </v-card-text>
        <v-card-actions class = "justify-center">
            <v-btn
                v-if = "canBeEditing"
                :loading = "isLoading"
                :text = "$t('edit')"
                color = "primary"
                variant = "elevated"
                @click = "startEditingApplication"
                prepend-icon = "mdi-pencil"
            />
        </v-card-actions>
    
    </v-card>
</template>
<script lang = "ts">
import MyCardApplicationFiles from "@components/MyCardApplicationFiles.vue";
import type CardApplication from "@models/CardApplication";
import type CardApplicationUpdate from "@models/CardApplicationUpdate";
import type { PropertyType } from "@types/globals";
import CardApplicantInfo from "@components/needUpdate/cardApplicantInfo.vue";
import { mapGetters } from "vuex";

export default {
	name :       "ApplicationPreview",
	components : {
		CardApplicantInfo,
		MyCardApplicationFiles,
	},
	emits :    [
		"edit",
	],
	props :      {
		application : {
			type :     Object as () => CardApplication,
			required : true,
		},
		canBeEditing : {
			type :    Boolean,
			default : true,
		}
	},
	data() {
		return {
			loadings : [] as boolean[],
			panel : [ 0 ],
		};
	},
	computed : {
		comment() : PropertyType<string> {
			return ( this.application.card_applicant_update_latest as CardApplicationUpdate )?.comment;
		},
		isLoading() : Boolean {
			return this.loadings.length !== 0;
		},
		// canBeEditing() : Boolean {
		// 	const t = this.application?.card_last_update;
		// 	return this.application?.canBeEdited ?? false;
		// },
		...mapGetters( "auth", [ "currentUser" ] ),
	},
	methods :  {
		startEditingApplication() {
			this.loadings.push( true );
			this.application.requestToEdit()
			    .then( ( update ) => {
				    console.debug( "startEditingApplication", this.application.card_last_update, update );
				    this.application.card_last_update = update;
				    this.$emit( "edit" );

			    } )
			    .finally(
				    () => this.loadings.pop(),
			    );
		},
	},
};
</script>
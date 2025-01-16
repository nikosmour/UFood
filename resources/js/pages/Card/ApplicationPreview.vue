<template>
    <v-card
        v-if = "application" :loading = "isLoading" :title = " $t('pendingReview')"
        class = "justify-content-around"
    >
        <CardApplicantInfo :user = "currentUser" />
        <MyCardApplicationFiles
            :application = "application"
            :loadings = "loadings"
            :isApplicationPeriodOpen = "isApplicationPeriodOpen"
        />
        
        <v-textarea
            v-if = "comment"
            v-model = "comment"
            :label = "$t('comment.value')"
            auto-grow
            disabled
            variant = "outlined"
        />
        <v-card-actions class = "justify-center">
            <v-btn
                v-if = "canBeEditing && isApplicationPeriodOpen"
                :loading = "isLoading"
                :text = "$t('edit')"
                color = "primary"
                variant = "elevated"
                @click = "startEditingApplication"
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
		isApplicationPeriodOpen : {
			type :    Boolean,
			default : true,
		},
	},
	data() {
		return {
			loadings : [] as boolean[],
		};
	},
	computed : {
		comment() : PropertyType<string> {
			return ( this.application.card_applicant_update_latest as CardApplicationUpdate )?.comment;
		},
		isLoading() : Boolean {
			return this.loadings.length !== 0;
		},
		canBeEditing() : Boolean {
			return this.application.canBeEdited;
		},
		...mapGetters( "auth", [ "currentUser" ] ),
	},
	methods :  {
		startEditingApplication() {
			this.loadings.push( true );
			this.application.requestToEdit()
			    .then( () => this.$emit( "edit" ) )
			    .finally(
				    () => this.loadings.pop(),
			    );
		},
	},
};
</script>
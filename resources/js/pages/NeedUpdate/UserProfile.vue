<template>
    <v-container max-width = "50rem">
        <v-card :title = "$t('personalInfo')" class = "pa-10">
            <card-applicant-info :user = "currentUser" class = "mt-5" />

            <create-user v-if = "isNew" />
        </v-card>

    </v-container>
</template>

<script>
import { mapGetters } from "vuex";
import CreateUser from "@components/CreateUser.vue";
import CardApplicantInfo from "@components/needUpdate/cardApplicantInfo.vue";

export default {
	components : {
		CardApplicantInfo,
		CreateUser,
	},
	computed :   {
		...mapGetters( "auth", [
			"currentUser",
			"isNew",
		] ),
	},
};
</script>

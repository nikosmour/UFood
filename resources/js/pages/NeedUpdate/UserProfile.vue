<template>
    <v-container>
        <card-applicant-info :user = "currentUser" />

        <create-user v-if = "isNew" />

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

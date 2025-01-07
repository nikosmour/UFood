<template>
    <v-container>
        <my-models-to-table :caption = "$t('user.information')" :models = "[user.user]" />
        <my-models-to-table
            v-if = "user.card_applicant" :caption = "$t('model_data.card_applicant')" :models = "[user.card_applicant]"
        />
        <my-models-to-table
            v-if = "user.addresses" :caption = "$t('model_data.addresses')" :models = "user.addresses"
        />


        <create-user v-if = "isNew" />

    </v-container>
</template>

<script>
import { mapGetters } from "vuex";
import MyModelsToTable from "@components/MyModelsToTable.vue";
import CreateUser from "@components/CreateUser.vue";

export default {
	components : {
		CreateUser,
		MyModelsToTable,
	},
	computed :   {
		...mapGetters( "auth", [
			"currentUser",
			"isNew",
		] ),
		user() {

			const user = this.currentUser.toObject( false );
			delete user.abilities;
			delete user.coupon_owner;
			const addresses = user.card_applicant?.addresses;
			delete user.card_applicant?.addresses;
			const card_applicant = user.card_applicant;
			delete card_applicant?.current_card_application;
			delete card_applicant?.valid_card_application;
			delete user.card_applicant;
			if ( card_applicant ) card_applicant.first_year = card_applicant.first_year.getFullYear();
			user.status = this.$t( "status." + user.status.key );
			return {
				user,
				card_applicant,
				addresses,
			};
		},
	},
};
</script>

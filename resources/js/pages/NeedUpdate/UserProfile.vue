<template>
    <v-container>
        <my-models-to-table :caption = "$t('user.information')" :models = "currentUser? [user]:[]" />
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
			user.status = this.$t( "status." + user.status.key );
			return user;
		},
	},
};
</script>

<template>
    <div>
        <h1>{{ $t( "Welcome" ) }}, {{ currentUser?.name }}!</h1>
        <my-models-to-table :caption = "$t('user.information')" :models = "currentUser? [user]:[]" />
    </div>
</template>

<script>
import { mapGetters } from "vuex";
import MyModelsToTable from "@components/MyModelsToTable.vue";

export default {
	components : { MyModelsToTable },
	computed :   {
		...mapGetters( "auth", [
			"currentUser",
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

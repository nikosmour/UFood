<script lang = " ts">
import MyFreeFoodStatus from "@components/MyFreeFoodStatus.vue";
import MyCouponOwnerBalance from "@components/MyCouponOwnerBalance.vue";
import { mapGetters } from "vuex";
import { defineComponent } from "vue";

export default defineComponent( {
	                                name : "AcademicStartPage",
	                                components : {
		                                MyCouponOwnerBalance,
		                                MyFreeFoodStatus,
	                                },
	                                computed : {
		                                ...mapGetters( "auth", [
			                                "currentUser",
		                                ] ),
	                                },
	                                methods : {
		                                ...mapGetters( "auth", [
			                                "hasAbility",
		                                ] ),
	                                },
	                                created() {

	                                },
                                } );
</script>

<template>
    <v-container max-width = "80em">
        <v-row>
            <v-col cols = "12" lg = "6">
                <my-free-food-status v-if = "hasAbility($enums.UserAbilityEnum.CARD_OWNERSHIP)" />
            </v-col>
            <v-col cols = "12" lg = "6">
                <my-coupon-owner-balance :coupon-owner = "currentUser.coupon_owner"></my-coupon-owner-balance>
            </v-col>
        </v-row>
    </v-container>
</template>
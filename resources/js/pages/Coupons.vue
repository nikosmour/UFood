<script>
import { mapState } from "vuex";
import MyCouponOwnerBalance from "@components/MyCouponOwnerBalance.vue";

export default {
	components : { MyCouponOwnerBalance },
	computed :   {
		...mapState( {
			             couponOwner : state => state.auth.user.coupon_owner,
		             } ),
	},
};
</script>

<template>
    <div>
        <my-coupon-owner-balance :coupon-owner = "couponOwner" />
        <router-view :couponOwner = "couponOwner" />
    </div>
</template>


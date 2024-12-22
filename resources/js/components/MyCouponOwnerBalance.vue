<template>
    <v-container>
        <v-card :aria-label = "$t('coupon.owner-balance')" :title = "$t('coupon.value',2)">
            <v-card-subtitle class = "mt-2">{{ $t( "balance" ) }}</v-card-subtitle>
            <v-card-item>
                <v-list class = "d-flex justify-space-around">
                    <v-list-item
                        v-for = "key in Object.keys($enums.MealPlanPeriodEnum)" :key = "key"
                        :title = "$t( 'meals.' + key.toLowerCase()) + ' '  +couponOwner[key]"
                    />
                </v-list>
            </v-card-item>
            <v-card-actions class = "d-flex justify-space-around">
                <v-btn
                    v-if = "$route.name !== 'coupons.History'"
                    :text = "$t('history')"
                    :to = "{ name: 'coupons.History' }"
                    color = "primary"
                    variant = "elevated"
                />
                <v-btn
                    v-if = "$route.name !== 'coupons.transfer'"
                    :text = "$t('transfer.value')"
                    :to = "{ name: 'coupons.transfer' }"
                    color = "primary"
                    variant = "elevated"
                />
            </v-card-actions>
        </v-card>
    </v-container>
</template>
<script>
export default {
	name :  "MyCouponOwnerBalance",
	props : {
		couponOwner : {
			type :     Object,
			required : true,
		},
	},
};
</script>
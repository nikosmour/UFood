<template>
    <transaction-coupon-form :couponOwner="couponOwner" transaction='transfer'
                             @new_transaction_coupon="new_transfer($event)"/>
</template>

<script>

import TransactionCouponForm from "../components/transactionCouponForm.vue";

export default {
    components: {TransactionCouponForm},
    props: {
        couponOwner: {
            type: Object,
            default: {}
        },
    },
    methods: {
        new_transfer(data) {
            for (let meal in this.$enums.MealPlanPeriodEnum)
                this.couponOwner[meal] -= data[meal];
        }
    }

}
</script>

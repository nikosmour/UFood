<template>
    <my-transaction-coupon-form
        :couponOwner="couponOwner"
        transaction="transfer"
        @new_transaction_coupon="handleNewTransfer"
    />
</template>

<script>
import MyTransactionCouponForm from "../components/MyTransactionCouponForm.vue";

export default {
    components: {
        MyTransactionCouponForm
    },
    props: {
        /**
         * The owner of the coupon, containing data about their balance per meal period.
         */
        couponOwner: {
            type: Object,
            required: true
        }
    },
    methods: {
        /**
         * Updates the balance of each meal period for the coupon owner
         * based on the values in the new transaction data.
         *
         * @param {Object} transactionData - Object containing changes in balance per meal period.
         */
        handleNewTransfer(transactionData) {
            Object.keys(this.$enums.MealPlanPeriodEnum).forEach(mealPeriod => {
                if (this.couponOwner.hasOwnProperty(mealPeriod)) {
                    this.couponOwner[mealPeriod] -= transactionData[mealPeriod] || 0;
                } else {
                    console.warn(`Meal period '${mealPeriod}' not found in couponOwner.`);
                }
            });
        }
    }
};
</script>

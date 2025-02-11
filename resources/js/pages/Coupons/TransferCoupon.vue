<template>
    <my-transaction-coupon-form
        :couponOwner = "couponOwner"
        transaction = "transfer"
        @new_transaction_coupon = "handleNewTransfer"
        step1 = "transaction.transfer"
    />
</template>

<script>
import MyTransactionCouponForm from "@components/MyTransactionCouponForm.vue";

export default {
	name : "TransferCoupon",
	components : {
		MyTransactionCouponForm,
	},
	props :      {
		/**
		 * The owner of the coupon, containing data about their balance per meal period.
		 */
		couponOwner : {
			type :     Object,
			required : true,
		},
	},
	methods :    {
		/**
		 * Updates the balance of each meal period for the coupon owner
		 * based on the values in the new transaction data.
		 *
		 * @param {Object} transactionData - Object containing changes in balance per meal period.
		 */
		handleNewTransfer( transactionData ) {
			const meals = this.$enums.MealPlanPeriodEnum;
			for ( const meal in meals )
				transactionData[ meal ] = -( transactionData[ meal ] || 0 );
			transactionData[ "transaction" ] = "sending";
			transactionData[ "created_at" ] = new Date();
			this.couponOwner.manageNewTransaction( transactionData, meals, this.couponOwner );
		},
	},
};
</script>

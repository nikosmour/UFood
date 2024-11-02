<template>
    <div class="row h-100">
        <transaction-coupon-form transaction='purchase'
                                 @new_transaction_coupon="newPurchase($event)"></transaction-coupon-form>
        <export-statistics-form :statistics="statistics"></export-statistics-form>
    </div>
</template>

<script>

import TransactionCouponForm from "../components/transactionCouponForm.vue";

export default {
    components: {
        TransactionCouponForm,
    },
    props: {
        statisticsServer: {
            type: Object,
        }
    },
    data: function () {
        return {
            statistics: null,
        }
    },
    methods: {
        async fetchData() {
            return await this.$axios.get(this.route('coupons.purchase.create')).then(
                response => {
                    return response.data
                }
            );
            // return {cards: 0, coupons: 0,}
        },
        newPurchase(purchaseInfo) {
            for (const category in this.statistics) {
                this.statistics[category] += purchaseInfo[category];
            }
        }
    },
    async mounted() {
        this.statistics = this.statisticsServer || (await this.fetchData());
    }
};
</script>

<style scoped>
/* Ensure responsive design */
.row.my_flex_height {
    display: flex;
    flex-direction: column;
}

@media (min-width: 768px) {
    .row.my_flex_height {
        flex-direction: row;
    }
}
</style>

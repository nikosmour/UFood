<template>
    <v-row>
        <v-col cols="12" md="8">
            <transaction-coupon-form
                transaction="purchase"
                @new_transaction_coupon="newPurchase"
            ></transaction-coupon-form>
        </v-col>
        <v-col class="d-flex align-center" cols="12" md="4">
            <export-statistics-form :statistics="statistics"></export-statistics-form>
        </v-col>
    </v-row>
</template>

<script>

import TransactionCouponForm from "../components/transactionCouponForm.vue";
import ExportStatisticsForm from "../components/ExportStatisticsForm.vue";

export default {
    components: {
        ExportStatisticsForm,
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
</style>

<template>
    <div class="row h-100">
        <transaction-coupon-form :customClass="['col-xm-12',' col-sm-6 ','col-md-7','col-lg-8'] " transaction='purchase'
                                 @new_purchase="newPurchase($event)"></transaction-coupon-form>
        <export-statistics-form :statistics="statistics"></export-statistics-form>
    </div>
</template>

<script>

export default {
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

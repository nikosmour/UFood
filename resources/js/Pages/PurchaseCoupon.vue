<template>
    <div class="row my_flex_height">
        <purchase-coupon-form @newPurchase="newPurchase($event)"></purchase-coupon-form>
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
            return await axios.get(route('coupons.purchase.create')).then(
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

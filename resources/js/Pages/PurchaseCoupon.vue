<template>
    <div class="row my_flex_height">
        <purchase-coupon-form v-bind:url="urls.form" v-on:newPurchase="newPurchase($event)"></purchase-coupon-form>
        <export-statistics-form v-bind:show_free_food="false" v-bind:statistics="statistics"
                                v-bind:url="urls.statistics"></export-statistics-form>
    </div>
</template>

<script>
export default {
    props: {
        urls: {
            type: Object,
            form: String,
            statistics: String
        },
        statisticsServer: {
            type: Object,
            breakfast: 0,
            lunch: 0,
            dinner: 0,
        }

    },
    data: function () {
        return {
            statistics: this.statisticsServer
        }
    },
    methods: {
        newPurchase(purchaseInfo) {
            for (const category in this.statistics) {
                this.statistics[category] += purchaseInfo[category];
            }
        }
    }
}
</script>

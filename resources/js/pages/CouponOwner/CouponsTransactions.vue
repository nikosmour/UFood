<script>
import CouponTransactionService from "../../services/CouponTransactionService.js";
import MyInfiniteScroll from "../../components/MyInfiniteScroll.vue";

export default {
    name: "CouponsTransactions",
    components: {MyInfiniteScroll},
    props: {
        /**
         * The owner of the coupon, containing balance information for each meal period.
         * @type {Object}
         */
        couponOwner: {
            type: Object,
            required: true,
            default: () => ({}),
        },
    },
    data() {
        return {
            transactions: [],
            expanded: [],
            isLoading: false,
            transactionService: null,
            stopFetch: false,
        };
    },
    computed: {
        /**
         * Table headers for the transactions data table.
         * @returns {Array<Object>} List of headers for v-data-table
         */
        tableHeaders() {
            return [
                {title: this.$t("transaction.kind"), value: "transaction"},
                {title: this.$t("date"), value: "date"},
                {title: this.$t("quantity", 2), value: "quantities"},
                {title: this.$t("balance"), value: "balance"},
                /*{title: this.$t("money"), value: "totalMoney"},
                ...Object.keys(this.$enums.MealPlanPeriodEnum).map(meal => ({
                    title: this.$t("meals." + meal.toLowerCase()),
                    value: `total.${meal}`,
                })),*/
            ];
        },
    },
    methods: {
        /**
         * Fetches transaction data from the server and formats it for display.
         * Pushes newly fetched transactions to the transactions array.
         *
         * @returns {void} This method doesn't return anything. It updates the state of the component.
         * @throws {Error} Throws an error if the fetch request fails.
         */
        async fetchData() {
            console.log('CouponTransactions.FetchData');
            if (this.isLoading || !this.transactionService) return;
            this.isLoading = true;

            try {
                const {transactions, stopFetch} = await this.transactionService.fetchTransactions()
                this.transactions.push(...transactions);
                this.stopFetch = stopFetch;
            } finally {
                this.isLoading = false;
            }
        },
    },
    mounted() {
        this.transactionService = new CouponTransactionService(this.$axios, this.route("coupons.history"), this.$enums, this.couponOwner);
        this.fetchData();
    },
};
</script>

<template>
    <v-container>
        <!-- Table for Transactions -->
        <v-data-table-virtual
            v-model:expanded="expanded"
            :aria-label="$t('transactions')"
            :headers="tableHeaders"
            :item-key="'id'"
            :items="transactions"
            class="elevation-1"
            hide-default-footer
            show-expand
        >
            <template #top>
                <v-toolbar :title="$t('transactions')" flat/>
            </template>

            <template #expanded-row="{ item }">
                <tr>
                    <td :colspan="tableHeaders.length + 1">
                        <v-row>
                            <v-col cols="6">
                                <span>
                                    {{ $t('transaction.id') }}: {{ item.id }}
                                </span>
                            </v-col>
                            <v-col v-if="item.transaction === 'receiving'" cols="auto">
                                <span>
                                    {{ $t('sender') }}: {{ item.other_person_id }}
                                </span>
                            </v-col>
                            <v-col v-else-if="item.transaction === 'sending'" cols="auto">
                                <span>
                                    {{ $t('receiver.value') }}: {{ item.other_person_id }}
                                </span>
                            </v-col>
                        </v-row>
                    </td>
                </tr>
            </template>

            <template #item.transaction="{ item }">
                <span>{{ $t('transaction.' + item.transaction) }}</span>
            </template>

            <template #item.quantities="{ item }">
                <div>
                    <span v-if="item.money !== 0">{{ item.money }}€</span>
                    <span v-for="(value, meal) in $enums.MealPlanPeriodEnum" :key="meal">
                        {{ item[meal] }}&nbsp;{{ $t('meals.' + meal.toLowerCase()) }}&nbsp;
                    </span>
                </div>
            </template>

            <template #item.balance="{ item }">
                <div>
                    <span v-if="item.totalMoney !== 0">{{ item.totalMoney }}€ </span>
                    <span v-for="(value, meal) in $enums.MealPlanPeriodEnum" :key="meal">
                        {{ item['total.' + meal] }}&nbsp;{{ $t('meals.' + meal.toLowerCase()) }}&nbsp;
                    </span>
                </div>
            </template>

            <template #item.date="{ item }">
                <span>{{ new Date(item.created_at).toLocaleDateString() }}</span>
            </template>

            <!--            <template #item.totalMoney="{ item }">-->
            <!--                <span>{{ item.totalMoney }}€</span>-->
            <!--            </template>-->

            <!--            <template v-for="(value, meal) in $enums.MealPlanPeriodEnum" #[`item.total.${meal}`]="{ item }">-->
            <!--                <span>{{ item['total.' + meal] }}</span>-->
            <!--            </template>-->
            <!--            -->
        </v-data-table-virtual>
        <my-infinite-scroll
            :loading="isLoading"
            :stopScroll="stopFetch"
            @load="fetchData"/>
    </v-container>
</template>

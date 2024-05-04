<script>
import {mapActions} from "vuex";

export default {
    name: "CouponsTransactions",
    props: {
        couponOwner: Object
    },
    data() {
        return {
            transactions: null,
            temp: {...this.couponOwner},
        };
    },
    computed: {
        url() {
            return route('coupons.history');
        },
    },
    methods: {
        ...mapActions([
            'getUser'
        ]),
        fetchData() {
            axios.get(this.url).then(
                response => {
                    console.log(response.data);
                    let transactions = response.data.transactions;
                    transactions = Array.isArray(transactions) ? transactions : [transactions]
                    // transactions = this.reformatTransactions(transactions);
                    if (new Date(transactions[0].created_at) > new Date(this.couponOwner.updated_at))
                        this.getUser().then(response => {
                            this.temp = {...this.couponOwner};
                            this.transactions = this.reformatTransactions(transactions)
                        });
                    else
                        this.transactions = this.reformatTransactions(transactions);
                }
            );
        },
        calculateMoney(amount, transaction) {
            // Implement logic to format money (e.g., using a library)
            // console.log(transaction);
            if (transaction == 'buying')
                this.temp.money += amount;
            else if (transaction == 'sending')
                this.temp.money += amount;
            else if (transaction == 'receiving')
                this.temp.money -= amount;
            return this.temp.money;
        },
        calculateMeal(amount, transaction, meal) {
            // Implement logic to format money (e.g., using a library)
            // console.log(transaction);
            if (transaction == 'buying')
                this.temp[meal] -= amount;
            else if (transaction == 'sending')
                this.temp[meal] += amount;
            else if (transaction == 'receiving')
                this.temp[meal] -= amount;
            else if (transaction == 'using')
                this.temp[meal] += amount
            return this.temp[meal];
        },
        getMealValue(data, meal) {
            if (data.transaction === 'using' && data.academic_id === this.$enums.MealPlanPeriodEnum[meal]) {
                return 1;
            }
            return Number(data[meal]) || 0;
        },
        reformatTransactions(transactions) {
            transactions.forEach(transaction => {
                for (let meal in this.$enums.MealPlanPeriodEnum) {
                    transaction[meal] = this.getMealValue(transaction, meal);
                    transaction['total.' + meal] = this.calculateMeal(transaction[meal], transaction.transaction, meal);
                }
                transaction.totalMoney = this.calculateMoney(Number(transaction.money), transaction.transaction);
                // transaction.created_at = new Date(transaction.created_at);
            })
            return transactions;
        },
    },
    mounted() {
        this.fetchData();
    },

}
</script>

<template>
    <div v-if="transactions">
        <table class="table text-center  table-hover table-col-to-row-sm caption-top">
            <caption>{{ 'transactions' }}</caption>
            <thead class="thead-dark">
            <tr>
                <th scope="col">{{ 'Category' }}</th>
                <th scope="col">{{ 'Comments' }}</th>
                <th scope="col">{{ 'Money' }}</th>
                <th v-for="(value, meal) in $enums.MealPlanPeriodEnum" scope="col">{{ meal }}</th>
                <th scope="col">{{ 'Date' }}</th>
            </tr>
            </thead>
            <tbody>

            <tr v-for="transaction in transactions" :key="transaction.id">
                <th scope="row">{{ transaction.transaction }}</th>
                <td>{{ (transaction.money != 0) ? transaction.money + '€' : '' }}
                    <template v-for="(value, meal) in $enums.MealPlanPeriodEnum">
                        &nbsp;{{ meal }}: {{ transaction[meal] }}
                    </template>
                    <template v-if="transaction.transaction === 'receiving'">
                        &nbsp;{{ 'Sender' }}: {{ transaction.academic_id }}
                    </template>
                    <template v-else-if="transaction.transaction === 'sending'">
                        &nbsp;{{ 'Receiver' }}: {{ transaction.academic_id }}
                    </template>
                </td>
                <td>{{ transaction.totalMoney }} €</td>
                <td v-for="(value, meal) in $enums.MealPlanPeriodEnum"
                    :key='"transaction." +transaction.id +".meal."+ meal'>
                    {{ transaction['total.' + meal] }}
                </td>
                <td>{{ transaction.created_at }}</td>
            </tr>
            </tbody>
        </table>
    </div>
</template>

<style scoped>

</style>

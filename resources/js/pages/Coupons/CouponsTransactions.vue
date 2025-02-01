<script lang = "ts">
import CouponTransactionService from "@services/CouponTransactionService.js";
import MyInfiniteScroll from "@components/MyInfiniteScroll.vue";
import type CouponOwner from "@models/CouponOwner";
import type CouponTransaction from "@models/CouponTransaction";

interface DataState {
	transactions : Array<CouponTransaction>;
	expanded : Array<boolean>;
	isLoading : boolean;
	transactionService : CouponTransactionService | null; // Replace `any` with the correct type if available
	stopFetch : boolean;
}
export default {
	name :       "CouponsTransactions",
	components : { MyInfiniteScroll },
	props :      {
		/**
		 * The owner of the coupon, containing balance information for each meal period.
         o	 * @type {CouponOwner}
		 */
		couponOwner : {
            type: Object as () => CouponOwner,
			required : true,
		},
	},
	data() : DataState {
		return {
			transactions : [],
			expanded :     [],
			isLoading :          false,
			transactionService : null,
			stopFetch :          false,
		};
	},
	computed : {
		lang() {
			return navigator.language;
		},
		/**
		 * Table headers for the transactions data table.
		 * @returns List of headers for v-data-table
		 */
		tableHeaders() : { title : string, value : string }[] {
			return [
				{
					title : this.$t( "transaction.kind" ),
					value : "transaction",
				},
				{
					title : this.$t( "date" ),
					value : "date",
				},
				{
					title : this.$t( "quantity", 2 ),
					value : "quantities",
				},
				{
					title : this.$t( "balance" ),
					value : "balance",
				},
				/*{title: this.$t("money"), value: "totalMoney"},
                ...Object.keys(this.$enums.MealPlanPeriodEnum).map(meal => ({
                    title: this.$t("meals." + meal.toLowerCase()),
                    value: `total.${meal}`,
                })),*/
			];
		},
		/**
		 * Precomputed translations for meal names to avoid recalculating them.
		 * @returns {Object} A dictionary of translated meal names.
		 */
		mealTranslations() {
			return Object.keys( this.$enums.MealPlanPeriodEnum )
			             .reduce( ( acc, meal ) => {
				             acc[ meal ] = this.$t( "meals." + meal.toLowerCase() );
				             return acc;
			             }, {} );
		},
	},
	methods :  {
		/**
		 * Fetches transaction data from the server and formats it for display.
		 * Pushes newly fetched transactions to the transactions array.
		 *
		 * @returns {void} This method doesn't return anything. It updates the state of the component.
		 * @throws {Error} Throws an error if the fetch request fails.
		 */
		async fetchData() {
			console.log( "CouponTransactions.FetchData" );
			if ( this.isLoading || !this.transactionService ) return;
			this.isLoading = true;

			try {
				const {
					      transactions,
					      stopFetch,
				      } = await this.transactionService.fetchTransactions();
				this.transactions.push( ...transactions );
				this.stopFetch = stopFetch;
			} finally {
				this.isLoading = false;
			}
		},

		/**
		 * Computes quantities for all meal plans in an item, based on a given prefix.
		 *
		 * @param {Object} item - The data item containing meal quantities.
		 * @param {string} prefix - A prefix to prepend to meal keys (e.g., "total.").
		 * @returns {Array<MealQuantity>} An array of objects representing meal quantities.
		 * @typedef {Object} MealQuantity
		 * @property {string} key - A unique key for the meal quantity (e.g., item ID and meal name).
		 * @property {number} quantity - The quantity of the meal.
		 * @property {string} name - The translated name of the meal.
		 */
		mealQuantities( item, prefix = "" ) {
			const mealNames = this.mealTranslations; // Precompute translations
			return Object.keys( this.$enums.MealPlanPeriodEnum )
			             .filter( ( meal ) => item[ prefix + meal ] ) // Only include meals with a quantity
			             .map( ( meal ) => ( {
				             key :      `${ item.id }-${ prefix }${ meal }`,
				             quantity : item[ prefix + meal ],
				             name :     mealNames[ meal ], // Translated meal name,
			             } ) );
		},
	},
	mounted() {
		this.couponOwner.coupon_transactions ??= [];
		this.transactions = this.couponOwner.coupon_transactions;
		this.transactionService =
			new CouponTransactionService( this.$axios, this.route( "coupons.history" ), this.$enums, this.couponOwner );
		if ( this.transactions.length === 0 ) {
			this.transactionService =
				new CouponTransactionService( this.$axios, this.route( "coupons.history" ), this.$enums,
				                              this.couponOwner );
			this.fetchData();
		} else {
			this.transactionService =
				new CouponTransactionService( this.$axios, this.route( "coupons.history" ), this.$enums,
				                              this.couponOwner, this.transactions[ -1 ] );
		}

	}
};
</script>

<template>
    <v-container>
        <!-- Table for Transactions -->
        <v-data-table-virtual
            v-model:expanded = "expanded"
            :aria-label = "$t('transactions')"
            :headers = "tableHeaders"
            :item-key = "'id'"
            :items = "transactions"
            class = "elevation-1"
            :mobile = "null"
            hide-default-footer
            expandOnClick
            hideDefaultHeader
            hover
            show-expand
        >
            <template #top>
                <v-toolbar :title = "$t('transactions')" flat />
            </template>

            <template #expanded-row = "{ item }">
                <tr>
                    <td :colspan = "tableHeaders.length + 1">
                        <v-row>
                            <v-col cols = "6">
                                <span>
                                    {{ $t( "transaction.id" ) }}: {{ item.foul_id }}
                                </span>
                            </v-col>
                            <v-col v-if = "item.transaction === 'receiving'" cols = "auto">
                                <span>
                                    {{ $t( "sender" ) }}: {{ item.other_person_id }}
                                </span>
                            </v-col>
                            <v-col v-else-if = "item.transaction === 'sending'" cols = "auto">
                                <span>
                                    {{ $t( "receiver.value" ) }}: {{ item.other_person_id }}
                                </span>
                            </v-col>
                        </v-row>
                    </td>
                </tr>
            </template>

            <template #item.transaction = "{ item }">
                <span>{{ $t( "transaction." + item.transaction ) }}</span>
            </template>

            <template #item.quantities = "{ item }">
                <div>
                    <!--                    <span v-if = "item.money !== 0">{{ item.money }}€ </span>-->
                    <span v-for = "meal in mealQuantities(item)" :key = "meal.key">
                        {{ meal.quantity }}&nbsp;{{ meal.name }}&nbsp;
                    </span>
                </div>
            </template>

            <template #item.balance = "{ item }">
                <div>
                    <!--                    <span v-if = "item.totalMoney !== 0">{{ item.totalMoney }}€ </span>-->
                    <span v-for = "meal in mealQuantities(item,'total.')" :key = "meal.key">
                        {{ meal.quantity }}&nbsp;{{ meal.name }}&nbsp;
                    </span>
                </div>
            </template>

            <template #item.date = "{ item }">
                <span>{{ item.created_at.toLocaleString( lang ) }}</span>
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
            :loading = "isLoading"
            :stopScroll = "stopFetch"
            @load = "fetchData"
        />
    </v-container>
</template>

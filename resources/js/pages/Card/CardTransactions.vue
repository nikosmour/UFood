<template>
    <v-container>
        <!-- Table for Transactions -->
        <v-data-table-virtual
            :aria-label = "$t('transactions')"
            :headers = "tableHeaders"
            :item-key = "'id'"
            :items = "transactions"
            class = "elevation-1"
            hide-default-footer
        >
            <template #top>
                <v-toolbar :title = "$t('transactions')" flat />
            </template>

            <template #item.quantity = "{ item }">
                -1&nbsp;{{ $t( "meals." + item.period.key.toLowerCase() ) }}
            </template>

            <template #item.date = "{ item }">
                {{ item.date.toLocaleDateString() }}
            </template>
        </v-data-table-virtual>

        <my-infinite-scroll
            :loading = "isLoading"
            :stopScroll = "stopFetch"
            @load = "fetchData"
        />
    </v-container>
</template>

<script>
import MyInfiniteScroll from "@components/MyInfiniteScroll.vue";
import UsageCard from "@models/UsageCard.js";

export default {
	name :       "CardTransactions",
	components : {
		MyInfiniteScroll,
	},
	data() {
		return {
			transactions : [], // List of transactions to display in the table
			isLoading :    false, // Loading state for fetch operation
			url :          this.route( "card.history" ), // API endpoint for fetching transactions
		};
	},
	computed : {
		/**
		 * Table headers for the transactions data table.
		 * @returns {Array<Object>} List of headers for v-data-table.
		 */
		tableHeaders() {
			return [
				{
					title : this.$t( "date" ),
					value : "date",
				},
				{
					title : this.$t( "quantity" ),
					value : "quantity",
				},
			];
		},

		stopFetch() {
			return !this.url;
		},
	},
	methods :  {
		/**
		 * Fetches transaction data from the server and updates the state.
		 * Handles pagination, formatting, and stopping the scroll.
		 *
		 * @returns {void} Updates transactions and pagination state.
		 */
		async fetchData() {
			console.log( "CardTransactions.FetchData" );
			if ( !this.url || this.isLoading ) return;
			this.isLoading = true;

			try {
				const response = await this.fetchTransactionsFromApi();
				this.processTransactionResponse( response );
			} finally {
				this.isLoading = false;
			}
		},

		/**
		 * Fetches transaction data from the API.
		 * @returns {Object} The API response containing transaction data.
		 * @throws {Error} Throws an error if the API request fails.
		 */
		async fetchTransactionsFromApi() {
			console.log( "Fetching data from:", this.url );
			return ( await this.$axios.get( this.url ) ).data.transactions;
		},

		/**
		 * Processes the transaction response, updating transactions and pagination state.
		 * @param {Object} response - The response object containing transaction data.
		 * @returns {void}
		 */
		processTransactionResponse( response ) {
			const transactions = Array.isArray( response.data )
			                     ? response.data.map( ( item ) => new UsageCard( item ) )
			                     : [];

			this.transactions.push( ...transactions );
			this.url = response.next_page_url; // Update the URL for the next fetch
		},
	},
	mounted() {
		this.fetchData(); // Initial fetch on component mount
	},
};
</script>

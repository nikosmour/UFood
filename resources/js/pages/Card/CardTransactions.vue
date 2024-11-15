<template>
    <div>
        <free-food-status />
        <my-models-to-table v-if = "transactions" :caption = "$t('transactions')" :models = "transactions" />
    </div>
</template>

<script>
import MyModelsToTable from "../../components/MyModelsToTable.vue";
import FreeFoodStatus from "../../components/FreeFoodStatus.vue";

export default {
	name :       "CardTransactions",
	components : {
		FreeFoodStatus,
		MyModelsToTable,
	},
	props :      {
		urlName : String,
	},
	data() {
		return {
			transactions : null,
		};
	},
	computed : {
		url() {
			return this.route( this.urlName );
		},
	},
	methods :  {
		fetchData() {
			this.$axios.get( this.url )
			    .then(
				    response => {
					    console.log( response.data );
					    let transactions = response.data.transactions;
					    this.transactions = Array.isArray( transactions )
					                        ? transactions
					                        : [ transactions ];
				    },
			    );
		},
	},
	mounted() {
		this.fetchData();
	},
	watch : {
		url() {
			this.fetchData();
		},
	},


};
</script>

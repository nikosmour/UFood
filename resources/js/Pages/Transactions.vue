<template>
    <div>
        <my-models-to-table v-if = "transactions" :caption = "$t('transactions')" :models = "transactions" />
    </div>
</template>

<script>
import MyModelsToTable from "@components/MyModelsToTable.vue";

export default {
	components : { MyModelsToTable },
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
		url( newValue ) {
			this.fetchData();
		},
	},


};
</script>

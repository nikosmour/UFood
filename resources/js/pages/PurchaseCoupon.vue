<template>
    <v-row>
        <v-col cols = "12" md = "8">
            <my-transaction-coupon-form
                transaction = "purchase"
                @new_transaction_coupon = "handleNewPurchase"
            />
        </v-col>
        <v-col class = "d-flex align-center" cols = "12" md = "4">
            <my-export-statistics-form :statistics = "statistics" />
        </v-col>
    </v-row>
</template>

<script>
import MyTransactionCouponForm from "@components/MyTransactionCouponForm.vue";
import MyExportStatisticsForm from "@components/MyExportStatisticsForm.vue";

export default {
	components : {
		MyExportStatisticsForm,
		MyTransactionCouponForm,
	},
	props :      {
		/**
		 * Initial statistics data passed from the server.
		 * This will be used to populate the statistics object if provided.
		 */
		statisticsServer : {
			type :     Object,
			required : false,
			default :  null,
		},
	},
	data() {
		return {
			/**
			 * Local statistics data used for displaying counts in the export form.
			 * Initialized either from server data or fetched via API.
			 */
			statistics : null,
		};
	},
	methods : {
		/**
		 * Fetches statistics data from the server if not provided via props.
		 * @returns {Object} The statistics data from the server.
		 */
		async fetchStatisticsData() {
			try {
				const response = await this.$axios.get( this.route( " coupons.purchase.create" ) );
				return response.data;
			} catch ( error ) {
				console.error( "Failed to fetch statistics data:", error );
				return {};
			}
		},

		/**
		 * Updates the statistics count for each purchase category.
		 * This method is triggered by the `transaction-coupon-form` component.
		 *
		 * @param {Object} purchaseInfo - An object with keys representing categories and values for count adjustments.
		 */
		handleNewPurchase( purchaseInfo ) {
			if ( this.statistics ) {
				Object.keys( this.statistics )
				      .forEach( category => {
					      if ( purchaseInfo[ category ] !== undefined ) {
						      this.statistics[ category ] += purchaseInfo[ category ];
					      } else {
						      console.warn( `Purchase category '${ category }' missing in purchaseInfo data.` );
					      }
				      } );
			} else {
				console.warn( "Statistics data is not initialized." );
			}
		},
	},
	async mounted() {
		/**
		 * Initializes the statistics data on component mount.
		 * If statistics data is provided via props, it is used directly;
		 * otherwise, data is fetched from the server.
		 */
		// this.statistics = this.statisticsServer || await this.fetchStatisticsData();
	},
};
</script>

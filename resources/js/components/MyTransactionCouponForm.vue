<template>
    <v-container fluid max-width = "50em">
        <v-row justify = "center">
            <v-col cols = "12">
                <v-stepper
                    v-if = "couponOwner"
                    v-model = "step"
                    :items = "[$t('receiver.value'), $t('confirmation'), $t('transaction.summary')]"
                    :mobile = "null"
                    mobile-breakpoint = "sm"
                    hide-actions
                >
                    <!-- Step 1: Receiver Form -->
                    <template v-slot:item.1>
                        <v-card :loading = "isLoading" :title = "$t('transaction.info')" flat>
                            <v-form
                                ref = "receiverForm" v-model = "isFormValid" validate-on = "invalid-input lazy"
                                @submit.prevent = "confirmDataTransactions"
                            >
                                <v-row>
                                    <!-- Receiver ID Field -->
                                    <v-col class = "mt-10" cols = "12" md = "8" offset-md = "2">
                                        <v-text-field
                                            ref = "receiverId"
                                            v-model.number = "receiver.id"
                                            :aria-label = "$t('receiver.value')"
                                            :error-messages = "errors.receiver_id"
                                            :label = "$t('receiver.value')"
                                            :rules = "rules.receiver"
                                            autofocus
                                            density = "compact"
                                            outlined
                                            required
                                            type = "number"
                                            validate-on-blur
                                            @input = "errors.receiver_id = null"
                                        ></v-text-field>
                                    </v-col>

                                    <!-- Meal Quantity Fields -->
                                    <v-col
                                        v-for = "(meal, index) in mealPlanPeriods" :key = "'form.meals.' + index"
                                        cols = "12" md = "8" offset-md = "2"
                                    >
                                        <v-text-field
                                            :ref = "`meals-${meal}`"
                                            v-model.number = "mealQuantities[meal]"
                                            :disabled = "mealsDisable"
                                            :error-messages = "errors[meal] || errors.meals"
                                            :label = "$t('meals.' + meal.toLowerCase())"
                                            :max = "couponOwner[meal] ?? null"
                                            :rules = "rules['meals'][meal]"
                                            density = "compact"
                                            min = "0"
                                            outlined
                                            type = "number"
                                            @input = "errors[meal] = errors['meals'] = null"
                                        ></v-text-field>
                                    </v-col>

                                    <!-- Next Button -->
                                    <v-col class = "d-flex justify-end" cols = "12">
                                        <v-btn
                                            :disabled = "isFormValid === false"
                                            :text = "$t('next') "
                                            color = "primary"
                                            type = "submit"
                                        />
                                    </v-col>
                                </v-row>
                            </v-form>
                        </v-card>
                    </template>

                    <!-- Step 2: Confirmation -->
                    <template v-slot:item.2>
                        <v-card :loading = "isLoading" :title = "$t('transaction.info')">
                            <v-card-text @keydown.enter = "handleSubmit">
                                <my-show-list-item :list-items = "listItems" />
                            </v-card-text>
                            <v-card-actions class = "d-flex justify-space-between">
                                <v-btn
                                    :text = "$t('previous')"
                                    color = "primary"
                                    variant = "text"
                                    @click = "step--"
                                />
                                <v-btn
                                    :text = "$t('confirm')"
                                    color = "primary"
                                    type = "submit"
                                    variant = "elevated"
                                    @click = "handleSubmit"
                                />
                            </v-card-actions>
                        </v-card>
                    </template>

                    <!-- Step 3: Transaction Summary -->
                    <template v-slot:item.3>
                        <v-card :title = "$t('transaction.info')">
                            <v-card-text @keydown.enter = "handleSubmit">
                                <my-show-list-item :list-items = "listItems" />
                            </v-card-text>
                            <v-card-actions class = "justify-center">
                                <v-btn
                                    :text = "$t('transaction.new')"
                                    color = "primary"
                                    variant = "elevated"
                                    @click = "resetForm"
                                />
                            </v-card-actions>
                        </v-card>
                    </template>
                </v-stepper>
            </v-col>
        </v-row>
    </v-container>
</template>

<script>
import MyShowListItem from "./MyShowListItem.vue";

export default {
	name :       "MyTransactionCouponForm",
	components : { MyShowListItem },

	/**
	 * Props passed to the component
	 */
	props : {
		/**
		 * The owner of the coupon. Contains details about available meals.
		 * @type {Object}
		 * @default {}
		 */
		couponOwner : {
			type :    Object,
			default : () => ( {} ),
		},

		/**
		 * Transaction type identifier
		 * @type {String}
		 */
		transaction : {
			type :     String,
			required : true,
		},
	},

	/**
	 * Component's local data
	 */
	data() {
		return {
			/**
			 * Current step in the transaction process
			 * @type {number}
			 */
			step : 1,

			/**
			 * Information about the receiver of the coupon
			 * @type {Object}
			 */
			receiver : {
				transaction_id : null,
				id :             "",
				name :           null,
				status :         null,
			},

			/**
			 * Stores quantities for each meal type
			 * @type {Object}
			 */
			mealQuantities : {},

			/**
			 * Validation errors
			 * @type {Object}
			 */
			errors : {
				meals : null,
			},

			/**
			 * Indicates if the form is valid
			 * @type {boolean}
			 */
			isFormValid : true,

			/**
			 * Loading state for the form submission
			 * @type {boolean}
			 */
			isLoading : false,

			/**
			 * API URL for submitting the transaction
			 * @type {string}
			 */
			url : this.route( `coupons.${ this.transaction }.store` ),

			/**
			 * Validation rules for form fields
			 * @type {Object}
			 */
			rules : {
				receiver : [
					value => !!value || this.$t( "validation.required", { attribute : this.$t( "receiver.value" ) } ),
					value => ( value > 0 ) || this.$t( "validation.exists",
					                                   { attribute : this.$t( "validation.attributes.receiver_id" ) } ),
					value => ( value !== this.couponOwner.academic_id ) || this.$t( "errors.transfer.myself" ),
				],
				meals :    {},
			},
		};
	},

	emits : [
		"new_transaction_coupon",
	],

	/**
	 * Computed properties for the component
	 */
	computed : {
		/**
		 * Items list for summary display
		 * @returns {Object}
		 */
		listItems() {
			return {
				receiver : this.receiver,
				meals :    this.mealQuantities,
			};
		},

		/**
		 * Periods for meal plans derived from enums
		 * @returns {Array}
		 */
		mealPlanPeriods() {
			return Object.keys( this.$enums.MealPlanPeriodEnum );
		},

		/**
		 * Indicates if meal inputs should be disabled based on receiver ID presence
		 * @returns {boolean}
		 */
		mealsDisable() {
			return !this.receiver.id;
		},
		lang() {
			return navigator.language;
		},
	},

	/**
	 * Methods for handling form logic and API interactions
	 */
	methods : {
		/**
		 * Focuses on the first invalid form field
		 */
		focusOnError() {
			if ( !this.$refs.receiverId.isValid ) {
				this.$refs.receiverId.focus();
				return;
			}
			for ( const meal of this.mealPlanPeriods ) {
				const mealRef = this.$refs[ `meals-${ meal }` ][ 0 ];
				if ( mealRef && !mealRef.isValid ) {
					mealRef.focus();
					return;
				}
			}
		},

		/**
		 * Submits data to the given URL and handles response or errors
		 * @param {string} url - URL to submit data to
		 * @param {Object} data - Data to submit
		 * @returns {Promise<Object>} Response data
		 */
		async submitData( url, data ) {
			if ( this.isLoading ) return;
			this.isLoading = true;
			try {
				const responseData = ( await this.$axios.post( url, data ) ).data;
				this.receiver.name = responseData.name;
				this.step++;
				return responseData;
			} catch ( error ) {
				if ( error.response?.status === 422 ) {
					this.errors = error.response.data.errors;
				}
				this.step = 1;
				this.$nextTick( this.focusOnError );
			} finally {
				this.isLoading = false;
			}
		},

		/**
		 * Handles form submission for confirmation
		 */
		handleSubmit() {
			const data = { receiver_id : this.receiver.id, ...this.mealQuantities };
			this.submitData( this.url, data )
			    .then( json => {
				    this.receiver.transaction_id = json.transaction;
				    this.$emit( "new_transaction_coupon", {
					    ...this.mealQuantities,
					    id :         Number( json.transaction.slice( 1 ) ),
					    created_at : ( new Date() ).toLocaleDateString( lang ),
				    } );
			    } );
		},

		/**
		 * Validates meal quantities based on coupon owner's maximum limits
		 * @param {string} key - Meal plan period key
		 * @returns {Function} Validation function for each meal field
		 */
		validateMeals( key ) {
			return newValue => {
				if ( newValue > ( this.couponOwner[ key ] ?? Infinity ) ) {
					return this.$t( "validation.max.numeric", {
						max :       this.couponOwner[ key ],
						attribute : this.$t( `meals.${ key }` ),
					} );
				}
				if ( newValue < 0 ) {
					return this.$t( "validation.min.numeric", {
						min :       0,
						attribute : this.$t( `meals.${ key }` ),
					} );
				}
				if ( newValue === 0 && Object.values( this.mealQuantities )
				                             .every( q => q === 0 ) ) {
					this.errors.meals = this.$t( "validation.at_least_one_greater_than_zero", {
						attribute : this.$t( "meal", 2 )
						                .toLocaleLowerCase(),
					} );
					return true;
				}
				this.errors.meals = null;
				return true;
			};
		},

		/**
		 * Confirms data transaction and moves to the next step
		 */
		async confirmDataTransactions() {
			await this.$refs.receiverForm.validate();
			if ( !this.isFormValid ) return;

			this.mealPlanPeriods.forEach( key => {
				this.mealQuantities[ key ] = this.mealQuantities[ key ] || 0;
			} );
			const data = {
				receiver_id : this.receiver.id,
				...this.mealQuantities,
			};
			this.submitData( this.route( "transaction.confirm" ), data )
			    .then( json => {
				    const status = this.$enums.UserStatusEnum.findByValue( json?.status )?.key ?? "";
				    if ( status )
					    this.receiver.status = this.$t( "status." + status );
			    } );
		},

		/**
		 * Resets the form and focuses on the first field
		 */
		resetForm() {
			this.$refs.receiverForm.reset();
			this.step = 1;
			this.receiver = {
				transaction_id : null,
				id :             "",
				name :           null,
				status :         null,
			};
			this.$nextTick( () => this.$refs.receiverId.focus() );
		},
	},

	/**
	 * Lifecycle hook that sets up validation rules for meals
	 */
	created() {
		this.mealPlanPeriods.forEach( key => {
			this.rules.meals[ key ] = [ this.validateMeals( key.toLowerCase() ) ];
		} );
	},
};
</script>

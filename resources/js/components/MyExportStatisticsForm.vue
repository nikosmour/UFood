<template>
    <v-container id = "statistics" class = "pa-4" max-width = "md">
        <!-- Meal Statistics Card -->
        <!--        <v-card class="mb-4" outlined>
                    <v-card-title>
                        <h5>{{ $t('statistics.meals') }}</h5>
                    </v-card-title>

                    <v-card-text>
                        <show-list-item :list-items="{'meals':statistics}" show-always-items='true'/>
                    </v-card-text>
                </v-card> -->

        <!-- Export Statistics Form -->
        <v-card
            id = "print_statistic_food" :loading = "isLoading" :title = "$t( 'statistics.export' )"
            class = "align-center"
        >
            <v-card-text class = "ma-4">
                <v-form
                    v-model = "isValid" :aria-label = "$t('statistics.export_form')"
                    @submit.prevent = "receive_statistics"
                >

                    <!-- Meal Period Selection -->
                    <v-select
                        v-model = "meal_period"
                        :items = "meal_export_periods"
                        :label = "$t('period.time')"
                        class = "mb-3"
                        hide-details
                    ></v-select>
                    <!-- Meal Category Selection (Checkboxes) -->
                    <fieldset
                        :aria-hidden = "!showMeals" :aria-label = "$t('meals.category')"
                        :class = "showMeals ? 'opacity-100':'opacity-0'"
                        class = "border-md mb-5"
                    >
                        <legend class = "pr-3 ml-3">

                            <v-checkbox
                                v-model = "selectAll"
                                :indeterminate = "isIndeterminate"
                                :label = "$t('export_statistics.select.all')"
                                hide-details = "auto"
                                @change = "toggleSelectAll"
                                @input = "errors.meal_category=null"
                            />
                        </legend>
                        <v-row class = "justify-space-between mr-1 mb-1">
                            <!--                        <v-text-field variant="outlined">
                                                        <template v-slot:label>
                                                    <v-checkbox
                                                        v-model="selectAll"
                                                        :indeterminate="isIndeterminate"
                                                        :label="$t('select.all')"
                                                        @change="toggleSelectAll"
                                                        class="select-all-checkbox"
                                                        hide-details
                                                    ></v-checkbox>
                                                        </template>-->

                            <v-col
                                v-for = "meal in meal_categories" :key = "'checkbox.'+meal" class = "pt-0 pb-0"
                                cols = "auto"
                            >

                                <v-checkbox
                                    v-model = "meal_category"
                                    :error-messages = "errors.meal_category"
                                    :label = "$t('meals.'+meal)"
                                    :value = "meal"
                                    hide-details = "auto"
                                    @input = "errors.meal_category=null"
                                ></v-checkbox>
                            </v-col>
                            <!--                        </v-text-field>-->
                        </v-row>
                    </fieldset>
                    <!-- Date Range Selection for Adapted Period -->
                    <v-row
                        :aria-hidden = "!showDates" :class = "showDates ? 'opacity-100':'opacity-0'"
                        :aria-description = "$t('export_statistics.select.dates')" class = "justify-center"
                    >
                        <v-col class = "pb-0" cols = "12" lg = "6" md = "12" sm = "6">
                            <v-text-field
                                v-model = "from_date"
                                :error-messages = "errors.from_date"
                                :label = "$t('from')"
                                :max = "now_date"
                                :rules = "rules.fromDateRules"
                                @input = "errors.from_date=null"
                                type = "text"
                            ></v-text-field>
                        </v-col>
                        <v-col cols = "12" lg = "6" md = "12" sm = "6">
                            <v-text-field
                                v-model = "to_date"
                                :error-messages = "errors.to_date"
                                :label = "$t('to')"
                                :max = "now_date"
                                :min = "from_date"
                                :rules = "rules.toDateRules"
                                @input = "errors.to_date=null"
                            ></v-text-field>
                        </v-col>
                        <!--                        <v-date-picker multiple="range" v-model="dates" />-->
                    </v-row>

                    <!-- Submission Button -->
                    <v-row justify = "center">
                        <v-col cols = "auto">
                            <v-btn :disabled = "!isValid" :loading = "isLoading" color = "primary" type = "submit">
                                {{ $t( "submit" ) }}
                            </v-btn>
                        </v-col>
                    </v-row>
                </v-form>
            </v-card-text>
        </v-card>

        <!-- Statistics Modal -->
        <my-statistics
            v-model:overlay = "overlayStatistics"
            v-bind:received_statistics = "received_statistics"
            v-on:update:overlay = "overlayStatistics = false"
        />
    </v-container>
</template>

<script>
import MyStatistics from "./MyExportStatisticsForm/MyStatistics.vue";

export default {
	name :       "MyExportStatisticsForm",
	components : {
		MyStatistics,
		// ShowListItem
	},
	props :      {
		/**
		 * Statistics data object passed as a prop.
		 * @type {Object}
		 */
		statistics : Object,

		/**
		 * Period options for meal statistics.
		 * @type {Array<string>}
		 * @default ['today', 'adapted']
		 */
		periods : {
			type :    Array,
			default : [
				"today",
				"adapted",
			],
		},
	},
	data() {
		let now = new Date().toISOString()
		                    .slice( 0, 10 );
		let $t = this.$t;
		return {
			meal_period :         null,
			meal_category :       [],
			selectAll :           false,
			isIndeterminate :     false,
			now_date :            now,
			from_date :           now,
			to_date :             now,
			received_statistics : null,
			isLoading :           false,
			overlayStatistics :   false,
			isValid :             true,
			errors :              {},
			rules :               {
				/**
				 * Validation rules for the 'from_date' field.
				 * @type {Array<Function>}
				 */
				fromDateRules : [
					v => !!v && !isNaN( new Date( v ).valueOf() ) || $t(
						"validation.date", { "attribute" : $t( "from" ) },
					),
					v => ( v <= this.to_date ) || $t(
						"validation.before_or_equal", {
							"date" :      this.to_date,
							"attribute" : $t( "from" ),
						},
					),
				],
				/**
				 * Validation rules for the 'to_date' field.
				 * @type {Array<Function>}
				 */
				toDateRules : [
					v => !!v && !isNaN( new Date( v ).valueOf() ) || $t(
						"validation.date", { "attribute" : $t( "to" ) },
					),
					v => ( v >= this.from_date ) || $t(
						"validation.after_or_equal", {
							"date" :      this.from_date,
							"attribute" : $t( "to" ),
						},
					),
					v => ( v <= this.now_date ) || $t(
						"validation.before_or_equal", {
							"date" :      this.now_date,
							"attribute" : $t( "to" ),
						},
					),
				],
				/**
				 * Validation rules for selecting meal categories.
				 * @type {Array<Function>}
				 */
				meal_category : [
					v => ( v.length ) || $t(
						"validation.min.array", {
							"min" :       1,
							"attribute" : $t( "meals" ),
						},
					),
				],
			},
		};
	},
	watch :    {
		/**
		 * Watcher for changes to 'meal_period'. Updates meal categories based on selected period.
		 */
		meal_period() {
			this.meal_category = this.meal_period === "current meal"
			                     ? [ "breakfast" ]
			                     : [
					"breakfast",
					"lunch",
					"dinner",
				];
		},

		/**
		 * Watcher for changes to 'meal_category'. Updates 'selectAll' and 'isIndeterminate' states.
		 * @param {Array<string>} newVal - New meal categories selected.
		 */
		meal_category( newVal ) {
			const length = newVal.length;
			this.selectAll = length === this.meal_categories.length;
			this.isIndeterminate = !this.selectAll && ( length > 0 );
		},
	},
	computed : {
		/**
		 * Returns the available meal categories.
		 * @type {Array<string>}
		 */
		meal_categories() {
			return [
				"breakfast",
				"lunch",
				"dinner",
			];
		},

		/**
		 * Returns the periods available for meal export.
		 * @type {Array<Object>}
		 */
		meal_export_periods() {
			return this.periods.map( period => ( {
				title : this.$t( "statistics." + period ),
				value : period,
			} ) );
		},

		/**
		 * Determines whether to show the date range selection based on selected meal period.
		 * @type {boolean}
		 */
		showDates() {
			return this.meal_period === "adapted";
		},

		/**
		 * Determines whether to show the meal categories based on selected meal period.
		 * @type {boolean}
		 */
		showMeals() {
			return this.meal_period !== "current meal";
		},
	},
	methods :  {
		/**
		 * Toggles the 'selectAll' state for meal categories.
		 * Selects all meal categories if 'selectAll' is true, or clears the selection if false.
		 */
		toggleSelectAll() {
			this.meal_category = this.selectAll
			                     ? [ ...this.meal_categories ]
			                     : [];
		},

		/**
		 * Handles the submission of the statistics form.
		 * Sends a POST request with selected meal period, categories, and date range.
		 */
		receive_statistics() {
			let params = new FormData();
			if ( this.meal_period === "current meal" ) {
				params.append( "current", true );
			} else {
				params.append( "from_date", this.from_date );
				params.append( "to_date", this.to_date );
				this.meal_category.forEach( category => {
					params.append( "meal_category[]", category );
				} );
			}

			this.isLoading = true;
			this.$axios
			    .post( this.route( "statistics" ), params )
			    .then( ( responseJson ) => {
				    this.received_statistics = responseJson[ "data" ];
				    this.overlayStatistics = true;
			    } )
			    .catch( ( error ) => {
				    if ( error.response && error.response.status === 422 )
					    this.errors = error.response.data.errors;
				    else
					    throw error;
			    } )
			    .finally( () => {
				    this.isLoading = false;
			    } );
		},
	},
	mounted() {
		/**
		 * Sets the initial value for 'meal_period' when the component is mounted.
		 */
		this.meal_period = this.periods[ 0 ];
	},
};
</script>

<style scoped>
.custom-checkbox {
    position: absolute;
    top: -1.8rem;
    left: 1rem;
    background: inherit;
    padding: 0 0.5rem;
}

</style>
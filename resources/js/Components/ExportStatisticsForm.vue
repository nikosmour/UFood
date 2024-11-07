<template>
    <v-container id="statistics" class="pa-4" max-width="md">
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
        <v-card id="print_statistic_food" :loading="isLoading" class="align-center">
            <v-card-title>
                <h5>{{ $t('statistics.export') }}</h5>
            </v-card-title>
            <v-card-text>
                <v-form v-model="isValid" :aria-label="$t('statistics.export_form')"
                        @submit.prevent="receive_statistics">

                    <!-- Meal Period Selection -->
                    <v-select
                        v-model="meal_period"
                        :items="meal_export_periods"
                        :label="$t('period.time')"
                        class="mb-3"
                        hide-details
                    ></v-select>
                    <!-- Meal Category Selection (Checkboxes) -->
                    <v-sheet v-if="meal_period !== 'current meal'" :aria-label="$t('meals.category')" border="lg"
                             class='mb-3'>

                        <v-checkbox
                            v-model="selectAll"
                            :indeterminate="isIndeterminate"
                            :label="$t('select.all')"
                            hide-details="true"
                            @change="toggleSelectAll"
                            @input="errors.meal_category=null"
                        />
                        <v-row class="justify-space-between">
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

                            <v-col v-for="meal in meal_categories" :key="'checkbox.'+meal" cols="auto">

                                <v-checkbox
                                    v-model="meal_category"
                                    :error-messages="errors.meal_category"
                                    :label="$t('meals.'+meal)"
                                    :value="meal"
                                    hide-details="true"
                                    @input="errors.meal_category=null"
                                ></v-checkbox>
                            </v-col>
                            <!--                        </v-text-field>-->
                        </v-row>
                        <v-messages
                            :active="!!errors.meal_category"
                            :messages="errors.meal_category"
                            color="error"
                        />
                    </v-sheet>
                    <!-- Date Range Selection for Adapted Period -->
                    <v-row v-if="meal_period === 'adapted'" class="mb-3">
                        <v-col>
                            <v-text-field
                                v-model="from_date"
                                :error-messages="errors.from_date"
                                :label="$t('from')"
                                :max="now_date"
                                :rules="rules.fromDateRules"
                                type="date"
                                @input="errors.from_date=null"
                            ></v-text-field>
                        </v-col>
                        <v-col>
                            <v-text-field
                                v-model="to_date"
                                :error-messages="errors.to_date"
                                :label="$t('to')"
                                :max="now_date"
                                :min="from_date"
                                :rules="rules.toDateRules"
                                type="date"
                                @input="errors.to_date=null"
                            ></v-text-field>
                        </v-col>
                        <!--                        <v-date-picker multiple="range" v-model="dates" />-->
                    </v-row>

                    <!-- Submission Button -->
                    <v-row justify="center">
                        <v-col cols="auto">
                            <v-btn :disabled="!isValid" color="primary" type="submit">
                                {{ $t('submit') }}
                            </v-btn>
                        </v-col>
                    </v-row>
                </v-form>
            </v-card-text>
        </v-card>

        <statistics v-if="shouldShowStatics" v-bind:html="new_page"
                    v-on:destroy="shouldShowStatics = false"></statistics>
    </v-container>
</template>

<script>
// import ShowListItem from "../components/ShowListItem.vue";
import Statistics from "./Statistics.vue";

export default {
    components: {
        Statistics,
        // ShowListItem
    },
    props: {
        statistics: Object,
    },
    data() {
        let now = new Date().toISOString().slice(0, 10);
        let $t = this.$t;
        return {
            meal_period: 'current meal',
            meal_category: [],
            selectAll: false,
            isIndeterminate: false,
            now_date: now,
            from_date: now,
            to_date: now,
            result: {
                message: this.$t('test.message'),
                success: true,
                hide: false,
                errors: [''],
            },
            new_page: null,
            isLoading: false,
            shouldShowStatics: false,
            isValid: true,
            errors: {},
            rules: {
                // Rule for from_date
                fromDateRules: [
                    v => !!v && !isNaN(new Date(v).valueOf()) || $t(
                        'validation.date', {'attribute': $t('from')}
                    ),
                    v => (v <= this.to_date) || $t(
                        'validation.before_or_equal', {'date': this.to_date, 'attribute': $t('from')}
                    ),
                ],
                // Rule for to_date
                toDateRules: [
                    v => !!v && !isNaN(new Date(v).valueOf()) || $t(
                        'validation.date', {'attribute': $t('to')}
                    ),
                    v => (v >= this.from_date) || $t(
                        'validation.after_or_equal', {'date': this.from_date, 'attribute': $t('to')}
                    ),
                    v => (v <= this.now_date) || $t(
                        'validation.before_or_equal', {'date': this.now_date, 'attribute': $t('to')}
                    ),
                ],
                meal_category: [
                    v => (v.length) || $t(
                        'validation.min.array', {'min': 1, 'attribute': $t('meals')}
                    ),
                ]
            },
        };
    },
    watch: {
        meal_period() {
            this.meal_category = this.meal_period === 'current meal' ? ['breakfast'] : ['breakfast', 'lunch', 'dinner'];
        },
        meal_category(newVal) {
            // Set selectAll to true if all checkboxes are selected, otherwise false
            const length = newVal.length;
            this.selectAll = length === this.meal_categories.length;
            this.isIndeterminate = !this.selectAll && (length > 0);
        },
    },
    computed: {
        meal_categories() {
            return ['breakfast', 'lunch', 'dinner'];
        },
        meal_export_periods() {
            const menus = ['current meal', 'today', 'adapted'];

            return menus.reduce((arr, menu) => {
                arr.push({
                    title: this.$t('statistics.' + menu),
                    value: menu,
                });
                return arr;
            }, []);
        },
    },
    methods: {
        toggleSelectAll() {
            // Toggle all checkboxes based on "Select All" state
            if (this.selectAll) {
                this.meal_category = [...this.meal_categories]
            } else {
                this.meal_category = []
            }
        },
        receive_statistics() {
            let params = new FormData();
            if (this.meal_period === 'current meal') {
                params.append('current', true);
            } else {
                params.append('from_date', this.from_date);
                params.append('to_date', this.to_date);
                this.meal_category.forEach((category) => {
                    params.append('meal_category[]', category);
                });
            }


            // params.append('meal_category',JSON.stringify(this.meal_category));
            this.result.message = ''; //#todo more clever way to show if the value is the same
            this.isLoading = true;

            this.$axios
                .post(this.route('statistics'), params)
                .then((responseJson) => {
                    this.new_page = responseJson['data'];
                    this.overlayStatistics = true;
                })
                .catch((error) => {
                    if (error.response && error.response.status === 422)
                        this.errors = error.response.data.errors;
                    else
                        throw error;
                })
                .finally(() => {
                    this.isLoading = false;
                });
        },
    },
    mounted() {
        this.meal_category = [this.meal_export_periods[0].title];

    },
};
</script>

<style scoped>
/*.meal-selection-container {
    border: 2px solid black;  !* Adds a black border around the div *!
    padding: 16px;  !* Adds some padding inside the div *!
    border-radius: 8px;  !* Optional: rounded corners for the div *!
    margin-bottom: 16px;  !* Optional: adds some space below the div *!
}
.meal-selection-container::before {
    content: '';                    !* Empty content *!
    position: absolute;             !* Absolutely position it inside the div *!
    top: 50%;                        !* Vertically center it relative to the div *!
    left: 0;
    right: 0;
    height: 2px;                    !* Set the height of the border line *!
    background-color: black;        !* Set the color of the border *!
    transform: translateY(-50%);     !* Align the border to the middle *!
    z-index: -1;                    !* Ensure the border is behind the checkbox *!
}

.v-checkbox {
    z-index: 1;                     !* Ensure checkboxes appear above the border *!
}*/


/*.v-row {
    margin-top: 50px;     !* Add some margin so that the checkboxes don't overlap the Select All *!
}

.v-checkbox {
    margin-top: 10px;     !* Space out the individual checkboxes *!
}*/

</style>

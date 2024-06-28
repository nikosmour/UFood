<template>
    <div id="statistics" class=" container col-xm-12 col-sm-6 col-md-5 col-lg-4 h-100">
        <div class="row justify-content-center h-100">
            <div class="col-12 h-100 d-flex flex-column">
                <div class="card">
                    <div class="card-header">
                        <h5 class="card-title">{{ $t('meal_statistics.value') }}</h5>
                    </div>

                    <div class="card-body">
                        <div v-for="(value, category) in statistics" :key="'statistics.' + category" class="row">
                            <label class="col-9 col-lg-7">{{ $t('meal_statistics.' + category.toLowerCase(), 2) }} => {{
                                    value
                                }}</label>
                        </div>
                    </div>
                </div>
                <div id="print_statistic_food" class="card flex-grow-1">
                    <div class="card-header">
                        <h5 class="card-title">{{ $t('meal_statistics.export') }}</h5>
                    </div>

                    <div class="card-body">
                        <form :aria-label="$t('meal_statistics.export_form')" method="GET"
                              @submit.prevent="check_id">
                            <loading v-if="isLoading"/>
                            <div class="mx-auto mb-3" style="min-width: 70%; max-width: 80%;">
                                <label class="sr-only" for="mealPeriodSelect">{{ $t('time period') }}</label>
                                <select id="mealPeriodSelect" v-model="meal_period" :aria-label="$t('time period')"
                                        class="col-12 form-control">
                                    <option v-for="period in meal_export_periods" :key="period" :value="period">{{
                                            $t('meal_statistics.' + period)
                                        }}
                                    </option>
                                </select>
                            </div>
                            <div v-if="meal_period !== 'current meal'" id="meal_category" class="mb-3"
                                 name="meal_category">
                                <label v-for="category in meal_categories" :key="category"
                                       class="checkbox-inline">
                                    <input v-model="meal_category" :name="category" :value="category"
                                           class="form-check-input"
                                           type="checkbox"/>
                                    {{ $t('meal_statistics.' + category) }}
                                </label>
                            </div>

                            <div v-if="meal_period === 'adapted'" id="choose_days_period" class="text-center mb-3"
                                 name="choose_days_period">
                                <label for="from_day">{{ $t('from') }}: <input id="from_day" v-model="from_date"
                                                                               class="form-control"
                                                                               type="date"/></label>
                                <label for="to_day">{{ $t('to') }}: <input id="to_day" v-model="to_date"
                                                                           class="form-control"
                                                                           type="date"/></label>
                            </div>
                            <div class="row mb-0">
                                <div class="col-md-8 offset-md-4">
                                    <button class="btn btn-primary" type="submit">{{ $t('submit') }}</button>
                                </div>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>

        <statistics v-if="shouldShowStatics" v-bind:html="new_page"
                    v-on:destroy="shouldShowStatics = false"></statistics>
    </div>
</template>

<script>
export default {
    props: {
        statistics: Object
    },
    data() {
        let now = new Date().toISOString().slice(0, 10);
        return {
            meal_period: 'current meal',
            meal_category: ['breakfast'],
            from_date: now,
            to_date: now,
            result: {
                message: this.$t('test.message'),
                success: true,
                hide: false,
                errors: ['']
            },
            new_page: null,
            isLoading: false,
            shouldShowStatics: false

        }
    },
    watch: {
        meal_period() {
            this.meal_category = this.meal_period == 'current meal' ? ['breakfast'] : ['breakfast', 'lunch', 'dinner'];
        }
    },
    computed: {
        meal_categories() {
            return ['breakfast', 'lunch', 'dinner'];
        },
        meal_export_periods() {
            return ['current meal', 'today', 'adapted'];
        }
    },
    methods: {
        check_id() {
            let params = new FormData();
            params.append('from_date', this.from_date);
            params.append('to_date', this.to_date);
            this.meal_category.forEach((category => {
                params.append('meal_category[]', category);
            }));


            // params.append('meal_category',JSON.stringify(this.meal_category));
            this.result.message = ''; //#todo more clever way to show if the value is the same
            this.isLoading = true;
            axios.post(route('statistics'), params
            ).then(responseJson => {
                const json = responseJson['data'];
                // window.open('', '_blank').document.write(json);
                this.new_page = json;
                this.shouldShowStatics = true;
                this.result.success = true;
                this.result.message = "allow the page to open and see the statistics";
                this.errors = [];
                // this.result.errors = json;
            }).catch(errors => {
                this.result.success = false;
                this.result.errors = errors.response.data.errors;
                console.log(errors.response.data.errors)
                this.result.message = "Request failed:";

            }).finally(() => {
                this.isLoading = false;

            });

        },
    }
}
</script>

<style scoped>
button.btn-primary {
    background-color: #0056b3; /* Darken the primary color */
    color: #ffffff;
}

button.btn-primary:hover {
    background-color: #004494; /* Darken the hover color */
}

@media (min-width: 768px) {
    #statistics {
        display: flex;
        flex-direction: column;
    }
}
</style>

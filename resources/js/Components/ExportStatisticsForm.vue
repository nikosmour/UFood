<template>
    <div id="statistics" class="col-xm-12 col-sm-6 col-md-5 col-lg-4">
        <div id="statistic_food" class="col-12">
            <header>
                <br/>
              <h4>{{ $t('meal_statistics.value') }}</h4>
            </header>
            <br/>
            <div v-for="(value, category) in statistics" :key="'statistics.' + category" class="row">
              <label class="col-9 col-lg-7">{{ $t('meal_statistics.' + category.toLowerCase(), 2) }} => {{
                  value
                }}</label>
            </div>
        </div>
        <div id="print_statistic_food" class="col-12">
            <header>
                <br/>
              <h4>{{ $t('meal_statistics.export') }}</h4>
            </header>
            <br/>
          <form :aria-label="$t('meal_statistics.export_form')" method="GET" @submit.prevent="check_id">
                <div class="mx-auto" style="min-width: 70%; max-width: 80%;">
                  <label class="sr-only" for="mealPeriodSelect">{{ $t('time period') }}</label>
                  <select id="mealPeriodSelect" v-model="meal_period" :aria-label="$t('time period')"
                          class="col-12 form-control">
                        <option v-for="period in meal_export_periods" :key="period" :value="period">{{
                            $t('meal_statistics.' + period)
                            }}
                        </option>
                    </select>
                    <div v-if="meal_period !== 'meal'" id="meal_category" name="meal_category">
                        <label v-for="category in meal_categories" :key="category" class="checkbox-inline">
                            <input v-model="meal_category" :name="category" :value="category" class="form-check-input"
                                   type="checkbox"/>
                          {{ $t('meal_statistics.' + category) }}
                        </label>
                    </div>
                </div>
                <div v-if="meal_period === 'adapted'" id="choose_days_period" class="text-center"
                     name="choose_days_period">
                    <label for="from_day">{{ $t('from') }}: <input id="from_day" v-model="from_date" class="form-control"
                                                                   type="date"/></label>
                    <label for="to_day">{{ $t('to') }}: <input id="to_day" v-model="to_date" class="form-control"
                                                               type="date"/></label>
                </div>
            <button aria-label="Submit" class="btn btn-primary col-12" type="submit">{{
                $t('status.submitted')
              }}
            </button>
                <message v-bind="result"></message>
            </form>
        </div>
      <statistics v-if="shouldShowStatics" v-bind:html="new_page" v-on:destroy="shouldShowStatics = false"></statistics>
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
            meal_period: 'meal',
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
            shouldShowStatics: false
        }
    },
    watch: {
        meal_period() {
            this.meal_category = this.meal_period == 'meal' ? ['breakfast'] : ['breakfast', 'lunch', 'dinner'];
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
            let vue = this;
            let params = new FormData();
            params.append('from_date', this.from_date);
            params.append('to_date', this.to_date);
            this.meal_category.forEach((category => {
                params.append('meal_category[]', category);
            }));


            // params.append('meal_category',JSON.stringify(this.meal_category));
            vue.result.message = ''; //#todo more clever way to show if the value is the same
            axios.post(route('statistics'), params
            ).then(function (responseJson) {
                const json = responseJson['data'];
                // window.open('', '_blank').document.write(json);
                vue.new_page = json;
                vue.shouldShowStatics = true;
                vue.result.success = true;
                vue.result.message = "allow the page to open and see the statistics";
                vue.errors = [];
                // vue.result.errors = json;
            }).catch(function (errors) {
                vue.result.success = false;
                vue.result.errors = errors.response.data.errors;
                console.log(errors.response.data.errors)
                vue.result.message = "Request failed:";

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

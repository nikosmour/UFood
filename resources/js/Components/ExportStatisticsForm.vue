<template>
    <div id=statistics class="col-xm-12 col-sm-6 col-md-5 col-lg-4  ">
        <div id="statistic_food " class=" col-12 ">
            <header>
                <br/>
                <h4>Στατιστικά γεύματος</h4>
            </header>
            <br/>
            <div v-for="(value, category) in statistics" :key="'statistics.'+category" class="row">
                <label class="col-9 col-lg-7">{{ category }} => {{ value }}</label>
            </div>
        </div>
        <div id="print_statistic_food " class=" col-12 ">
            <header>
                <br/>
                <h4>Εξαγωγή στατιστικών</h4>
            </header>
            <br/>
            <form method="GET" v-on:submit.prevent="check_id">
                <div class="mx-auto" style=" min-width: 70%; max-width:80%;">
                    <select v-model="meal_period" class=" col-12 ">
                        <option value="meal">Meal</option>
                        <option value="today">Today</option>
                        <option value="adapted">adapted</option>
                    </select>
                    <div v-if='meal_period!="meal"' id="meal_category" name="meal_category">
                        <label class="checkbox-inline "><input v-model="meal_category" name="breakfast" type="checkbox"
                                                               value="breakfast">Πρωινό</label>
                        <label class="checkbox-inline "><input v-model="meal_category" name="lunch" type="checkbox"
                                                               value="lunch">Μεσημεριανό</label>
                        <label class="checkbox-inline "><input v-model="meal_category" name="dinner" type="checkbox"
                                                               value="dinner">Βραδινό</label>
                    </div>
                </div>
                <div v-if='meal_period=="adapted"' id="choose_days_period" class="text-center"
                     name="choose_days_period">
                    <label class=" ">Από:<input id="from_day" v-model="from_date" type="date" value=""></label>
                    <label class=" ">Μέχρι:<input id="to_day" v-model="to_date" type="date" value=""></label>
                </div>
                <button class="  btn-primary col-12 " type="submit">Υποβολή</button>
                <message v-bind="result"></message>
            </form>
        </div>
        <statistics v-if="shouldShowStatics" v-bind:html="new_page"
                    v-on:destroy="this.shouldShowStatics = false"></statistics>

    </div>
</template>

<script>

export default {
    props: {
        url: String,
        show_free_food: Boolean,
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
                message: 'ready',
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
    computed: {},
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

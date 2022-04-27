<template>
    <div id=statistics class="col-xm-12 col-sm-6 col-md-5 col-lg-4  ">
        <div id="statistic_food " class=" col-12 ">
            <header>
                <br/>
                <h4>Στατιστικά γεύματος</h4>
            </header>
            <br/>
            <div class="row" v-for="(value, category) in statistics" :key="'statistics.'+category">
                <label class="col-9 col-lg-7">{{ category }} => {{ value }}</label>
            </div>
        </div>
        <div id="print_statistic_food " class=" col-12 ">
            <header>
                <br/>
                <h4>Εξαγωγή στατιστικών</h4>
            </header>
            <br/>
            <form v-bind:action=url target="_blank" method="GET">
                <div class="mx-auto" style=" min-width: 70%; max-width:80%;">
                    <select onchange="adaptedCheck(this);" name="eidos_geymatos" id="eidos_geymatos" class=" col-12 ">
                        <option value="meal">Γεύμα</option>
                        <option value="today">Σήμερα</option>
                        <option value="adapted">Προσαρμοσμένο</option>
                    </select>
                    <div name="choose_meals" id="choose_meals" class="invisible ">
                        <label class="checkbox-inline "><input type="checkbox" name="breakfast" value="breakfast"
                                                               checked>Πρωινό</label>
                        <label class="checkbox-inline "><input type="checkbox" name="lunch" value="lunch" checked>Μεσημεριανό</label>
                        <label class="checkbox-inline "><input type="checkbox" name="dinner" value="dinner" checked>Βραδινό</label>
                    </div>
                </div>
                <div name="choose_days_period" id="choose_days_period" class="text-center">
                    <label class=" ">Από:<input type="date" name="from_day" id="from_day" value=""></label>
                    <label class=" ">Μέχρι:<input type="date" name="to_day" id="to_day" value=""></label>
                </div>
                <button type="submit" class="  btn-primary col-12 ">Υποβολή</button>
            </form>
        </div>
    </div>
</template>

<script>
export default {
    props: {
        url: String,
        show_free_food: Boolean,
        statistics: Object
    },
    computed: {},
    methods: {
        adaptedCheck($value) {

        },

    }
}
</script>

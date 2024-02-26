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
            <form method="GET" target="_blank" v-bind:action=url>
                <div class="mx-auto" style=" min-width: 70%; max-width:80%;">
                    <select id="eidos_geymatos" class=" col-12 " name="eidos_geymatos" onchange="adaptedCheck(this);">
                        <option value="meal">Γεύμα</option>
                        <option value="today">Σήμερα</option>
                        <option value="adapted">Προσαρμοσμένο</option>
                    </select>
                    <div id="choose_meals" class="invisible " name="choose_meals">
                        <label class="checkbox-inline "><input checked name="breakfast" type="checkbox"
                                                               value="breakfast">Πρωινό</label>
                        <label class="checkbox-inline "><input checked name="lunch" type="checkbox" value="lunch">Μεσημεριανό</label>
                        <label class="checkbox-inline "><input checked name="dinner" type="checkbox" value="dinner">Βραδινό</label>
                    </div>
                </div>
                <div id="choose_days_period" class="text-center" name="choose_days_period">
                    <label class=" ">Από:<input id="from_day" name="from_day" type="date" value=""></label>
                    <label class=" ">Μέχρι:<input id="to_day" name="to_day" type="date" value=""></label>
                </div>
                <button class="  btn-primary col-12 " type="submit">Υποβολή</button>
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

<template>
    <div>
        <header>
            <br/>
            <h4 class="text-left">Πώληση κουπονιών</h4>
        </header>
        <form v-on:submit.prevent="coupons">
            <!--            <div class="form-group mx-auto " style="max-width:20em">-->
            <!--                <div class="col-sm md-form">-->
            <!--                    <label > Αριθμός κάρτας</label>-->
            <!--                    <input  v-bind:class="getClass/*, {'form-control':true}*/ " type="number" v-model.number='form_data.academic_id'  placeholder="Καταχωρίστε τον αριθμό της κάρτας"  min="0" autofocus required/>&lt;!&ndash;name="academic_id"&ndash;&gt;-->
            <!--                </div>-->
            <!--            </div>-->
            <div class="form-group form-row mx-auto  " style="max-width:30em">
                <div v-for="(value, category) in form_data" :key="'form_data_'+category" class="col-sm md-form">
                    <label>{{ category }}</label>
                    <input v-model.trim.number='form_data[category]' class="form-control" min="0" type="number"/>
                </div>
            </div>
            <message v-bind="result"></message>
            <div class="mx-auto" style="max-width:5em">
                <button class="btn btn-default" type="submit">Επόμενο</button>
            </div>
        </form>
    </div>
</template>

<script>
export default {
    props: {url: String},
    data: function () {
        return {
            form_data: {
                academic_id: '',
                BREAKFAST: 0,
                LUNCH: 0,
                DINNER: 0,
            },
            result: {
                message: this.url,
                success: true,
                hide: true,
                errors: []
            },
        }
    },
    methods: {
        coupons() {
            let vue = this;
            vue.result.message = ''; //#todo more clever way to show if the value is the same
            if (0 === vue.form_data.academic_id) {
                vue.result.success = false;
                vue.result.message = " Request failed:"; //#todo not enough time to change if the message was the same
                vue.result.errors = ["Provide a valid academic card"];
                return;
            }
            if (0 == vue.form_data.BREAKFAST && 0 == vue.form_data.LUNCH && 0 == vue.form_data.DINNER) {
                vue.result.success = false;
                vue.result.message = " Request failed: ";
                vue.result.errors = ["You need to sell something"];
                return;
            }
            if (confirm("Αγορά κουπονιών απο τον χρήστη με ακαδημαϊκή ταυτότητα: " + vue.form_data.academic_id +
                " Πρωινά: " + vue.form_data.BREAKFAST + " Γευμα: " + vue.form_data.LUNCH + " Δείπνο: " + vue.form_data.DINNER))
                axios.post(vue.url, vue.form_data
                ).then(function (responseJson) {
                    let json = responseJson['data'];
                    vue.result.success = json['sold'];
                    if (json['sold']) {
                        vue.result.message = "Επιτυχής πώληση";
                        vue.$emit('newPurchase', vue.form_data);
                        vue.result.errors = [];
                    } else {
                        vue.result.message = "Request failed:";
                        vue.result.errors = json;
                    }
                }).catch(function (errors) {
                    vue.result.success = false;
                    vue.result.errors = errors.response.data.errors;
                    console.log(errors.response.data.errors);
                    vue.result.message = "Request failed:";
                });
        }

    }
}
</script>

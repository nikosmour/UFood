<template>
    <div class="col-xm-12 col-sm-6 col-md-7 col-lg-8">
        <header class="mb-4">
            <h4 class="text-left">{{ $t('sale_coupons') }}</h4>
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
                    <label>{{ $t(`${category.charAt(0).toUpperCase() + category.slice(1).toLowerCase()}`) }}</label>
                    <input v-model.trim.number='form_data[category]' class="form-control" min="0" type="number"/>
                </div>
            </div>
            <message v-bind="result"></message>
            <div class="mx-auto" style="max-width: 5em;">
                <button class="btn btn-primary" type="submit">{{ $t('next') }}</button>
            </div>
        </form>
    </div>
</template>

<script>
import axios from 'axios';

export default {
    data() {
        return {
            form_data: {
                academic_id: '',
                BREAKFAST: 0,
                LUNCH: 0,
                DINNER: 0,
            },
            url: route('coupons.purchase.store'),
            result: {
                message: this.$t("test.message"),
                success: true,
                hide: true,
                errors: []
            },
        };
    },
    methods: {
        coupons() {
            if (this.form_data.academic_id === 0) {
                this.result.success = false;
                this.result.message = this.$t('request_failed');
                this.result.errors = [this.$t('provide_valid_card')];
                return;
            }
            if (this.form_data.BREAKFAST === 0 && this.form_data.LUNCH === 0 && this.form_data.DINNER === 0) {
                this.result.success = false;
                this.result.message = this.$t('request_failed');
                this.result.errors = [this.$t('errors.sell_something')];
                return;
            }
            if (confirm(this.$t('confirm_purchase', {
                academic_id: this.form_data.academic_id,
                BREAKFAST: this.form_data.BREAKFAST,
                LUNCH: this.form_data.LUNCH,
                DINNER: this.form_data.DINNER
            }))) {
                axios.post(this.url, this.form_data)
                    .then(responseJson => {
                        let json = responseJson.data;
                        this.result.success = json.sold;
                        if (json.sold) {
                            this.result.message = this.$t('successful_sale');
                            this.$emit('newPurchase', this.form_data);
                            this.result.errors = [];
                        } else {
                            this.result.message = this.$t('request_failed');
                            this.result.errors = json.errors;
                        }
                    })
                    .catch(errors => {
                        this.result.success = false;
                        this.result.errors = errors.response.data.errors;
                        this.result.message = this.$t('request_failed');
                    });
            }
        }
    }
};
</script>


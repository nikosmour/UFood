<template>
    <div class="container col-xm-12 col-sm-6 col-md-7 col-lg-8">
        <div class="row justify-content-center">
            <div class="col-md-11">
                <div class="card">
                    <div class="card-header">
                        <h5 class="card-title">{{
                                $t('sale_coupons') + ' - ' + $t(disableForm ? 'verification' : 'edit')
                            }}</h5>
                    </div>

                    <div class="card-body">
                        <form @submit.prevent="submitForm">
                            <loading v-if="isLoading"/>
                            <div v-for="(value, category) in form_data" :key="'form_data_'+category" class="row mb-3">
                                <label :for="category"
                                       class="col-md-4 col-form-label text-md-end">{{ $t(`${category.toLowerCase()}`) }}</label>
                                <div class="col-md-6">
                                    <input :id="category" v-model.trim.number='form_data[category]'
                                           :class="[{ 'is-invalid': errors[category] || errors.credentials },
                                           disableForm ? 'form-control-plaintext' : 'form-control',
                                           ]" :readonly="disableForm" min="0" required type="number">
                                    <div v-for=" error in errors[category]" class="invalid-feedback" role="alert">
                                        {{ $t(error) }}
                                    </div>
                                </div>
                            </div>
                            <message v-bind="result"></message>
                            <div class="row mb-0">
                                <div class="col-md-8 offset-md-4">
                                    <button v-if="disableForm" class="btn btn-primary" type="submit">{{
                                            $t('submit')
                                        }}
                                    </button>
                                    <button v-else class="btn btn-primary" type="button" v-on:click="validateForm">
                                        {{ $t('next') }}
                                    </button>
                                </div>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
</template>
<script>

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
            errors: {},
            isLoading: false,
            disableForm: false,
            result: {
                message: this.$t("test.message"),
                success: true,
                hide: true,
                errors: []
            },
        };
    },
    methods: {
        validateForm() {
            this.errors = {};
            this.result.message = '';
            if (this.form_data.academic_id === 0) {
                this.result.success = false;
                this.result.message = this.$t('request_failed');
                this.errors.academic_id = [this.$t('provide_valid_card')];
                return;
            }
            if (this.form_data.BREAKFAST === 0 && this.form_data.LUNCH === 0 && this.form_data.DINNER === 0) {
                this.result.success = false;
                this.result.message = this.$t('request_failed');
                this.errors.BREAKFAST = [this.$t('errors.at_least_one_greater_than_zero')];
                return;
            }
            this.disableForm = true
        },
        submitForm() {
                this.isLoading = true;
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
                        this.errors = errors.response.data.errors;
                        this.result.message = this.$t('request_failed');
                    })
                    .finally(() => {
                        // Reset form after login attempt
                        this.isLoading = false;
                        this.disableForm = false;
                    });
        }
    }
};
</script>

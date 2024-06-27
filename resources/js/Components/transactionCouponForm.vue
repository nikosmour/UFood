<template>
    <div :class="customClass" class="container">
        <div class=" row justify-content-center">
            <div class="">
                <div class="card">
                    <div class="card-header">
                        <h5 class="card-title">{{
                                $t(`${transaction}.value`) + ' - ' + $t(
                                    submitted ? "transaction.successful" : (disableForm ? 'verification' : 'edit')
                                )
                            }}</h5>
                    </div>

                    <div class="card-body">
                        <form :aria-label="$t(`${transaction}.coupon_form`)" @submit.prevent="handleSubmit">
                            <loading v-if="isLoading"/>
                            <div class="row mb-3">
                                <label class="col-md-4 col-form-label text-md-end" for="receiverId">
                                    {{ $t('receiver') }} </label>
                                <div class="col-md-6">
                                    <div v-if="submitted">
                                        {{ receiver.name }}
                                    </div>
                                    <input id="receiverId" v-model.number.trim="receiver.id"
                                           :class="[{ 'is-invalid': errors.receiver_id,'is-valid': valid.receiver_id && !errors.receiver_id  },
                                                disableForm ? 'form-control-plaintext' : 'form-control',
                                           ]" :readonly="disableForm"
                                           required
                                           type="number">
                                    <div v-for=" error in errors.receiver_id" class="invalid-feedback" role="alert">
                                        {{ $t(error) }}
                                    </div>

                                </div>
                            </div>

                            <div v-for="(meal, index) in mealPlanPeriods" :key="index" class="row mb-3">
                                <label :for="meal" class="col-md-4 col-form-label text-md-end">{{
                                        $t(meal.toLowerCase())
                                    }} </label>
                                <div class="col-md-6">
                                    <input :id="meal" v-model.number.trim="mealQuantities[meal]"
                                           :class="[{ 'is-invalid': errors[meal] ||errors.total_meals,
                                         'is-valid': valid[meal]  },
                                           disableForm ? 'form-control-plaintext' : 'form-control',
                                           ]"
                                           :disabled="!receiver.id" :max="couponOwner[meal] ?? null"
                                           :name="meal" :readonly="disableForm "
                                           min="0" required
                                           type="number" value="0">
                                    <div v-for=" error in errors[meal] || errors.total_meals" class="invalid-feedback"
                                         role="alert">
                                        {{ $t(error) }}
                                    </div>
                                </div>
                            </div>
                            <div class="row mb-0">
                                <div class="col-md-8 offset-md-4 ">
                                    <template v-if="disableForm &&!submitted">
                                        <button class="btn btn-primary me-3" type="submit">
                                            {{ $t('submit') }}
                                        </button>
                                        <button class="btn btn-outline-secondary" type="button"
                                                v-on:click="disableForm=false">
                                            {{ $t('back') }}
                                        </button>
                                    </template>
                                    <button v-else-if="submitted" class="btn btn-primary" type="button"
                                            v-on:click="resetForm">
                                        {{ $t('transaction.new') }}
                                    </button>
                                    <button v-else :disabled="!result.success " class="btn btn-primary"
                                            type="button" v-on:click="validateForm">
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
    <message v-bind="result"></message>
</template>

<script>

export default {
    props: {
        customClass: {
            type: Array || Object || String
        },
        couponOwner: {
            type: Object,
            default: {}
        },
        transaction: String
    },
    data() {
        return {
            result: {
                message: this.$t('test.message'),
                success: false,
                hide: true,
                errors: []
            },
            receiver: {
                'id': '',
                'name': null
            },
            mealQuantities: {}, // Object to store meal quantities
            errors: {},
            isLoading: false,
            disableForm: false,
            url: route(`coupons.${this.transaction}.store`),
            valid: {},
            submitted: false
        };
    },
    computed: {
        mealPlanPeriods: function () {
            return Object.keys(this.$enums.MealPlanPeriodEnum);
        },//Object.values(\App\Enum\MealPlanPeriodEnum::names()), // Get meal plan periods as array
    },
    methods: {
        handleSubmit() {
            const data = {
                receiver_id: this.receiver.id,
                ...this.mealQuantities,
            };
            this.isLoading = true;
            axios.post(this.url, data).then(responseJson => {
                let json = responseJson.data;
                this.receiver.name = json.receiver
                this.result.message = this.$t(`${this.transaction}.successful`);
                this.$emit(`new_${this.transaction}`, this.mealQuantities);
                this.submitted = true;


            }).catch(errors => {
                if (errors.response && errors.response.status === 422)
                    this.errors = errors.response.data.errors;
                else
                    this.result.errors = errors;
                this.result.message = this.$t('request_failed');
                this.disableForm = false;
            }).finally(() => {
                this.isLoading = false;
            });
        },
        resetForm() {
            this.mealPlanPeriods.forEach(key => {
                this.mealQuantities[key] = 0;
            });
            this.receiver.name = this.receiver.id = '';
            this.disableForm = this.submitted = false;
            this.errors = {};

        },
        validateMeals(newValue, oldValue, key) {
            if (this.submitted) return;
            this.valid[key] = false;
            if (this.couponOwner && newValue > this.couponOwner[key]) {
                this.errors[key] = [this.$t('errors.maxCoupons', {'max': this.couponOwner[key]})]
            } else if (newValue < 0 || newValue === '') {
                this.errors[key] = [this.$t('errors.minCoupons', {'min': 0})]
            } else if (newValue === 0 && (Object.keys(this.mealQuantities).reduce((sum, key) => sum + this.mealQuantities[key], 0) === 0)) {
                this.errors.total_meals = [this.$t('errors.at_least_one_greater_than_zero')];
            } else {
                delete this.errors.total_meals;
                delete this.errors[key];
                this.valid[key] = newValue
            }
        },
        validateForm() {
            this.errors = {};

            this.disableForm = this.result.success
        },
    },
    watch: {
        'receiver.id'(newValue) {
            this.valid['receiver_id'] = false;
            if (this.couponOwner && newValue === this.couponOwner.academic_id) {
                // Vue.set(this.errors,'receiver_id',[this.$t('errors.transfer.myself')])
                this.errors.receiver_id = [this.$t('errors.transfer.myself')];
            } else if (newValue < 1) {
                this.errors.receiver_id = [this.$t('provide_valid_card')];
            } else {
                this.valid['receiver_id'] = true;
                delete this.errors.receiver_id;
            }
        },
        errors: {
            handler(newValue) {
                console.log(newValue);
                if (Object.keys(newValue).length === 0) {
                    this.result.message = '';
                    this.result.success = true;
                    this.result.errors = null;
                } else {
                    this.result.success = false;
                    // this.result.message = this.$t('request_failed');
                }
            },
            deep: true,  // Enable deep watching
        },


    },
    created() {
        this.mealPlanPeriods.forEach(key => {
            this.mealQuantities[key] = 0;
            this.$watch(`mealQuantities.${key}`, (newValue, oldValue) => {
                this.validateMeals(newValue, oldValue, key);

            })
        });
    }
};
</script>

<style scoped>
/* Ensure sufficient color contrast */
button.btn-primary {
    background-color: #0056b3; /* Darken the primary color */
    color: #ffffff;
}

button.btn-primary:hover {
    background-color: #004494; /* Darken the hover color */
}
</style>

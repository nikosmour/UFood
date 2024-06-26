<template>
    <div v-if="couponOwner" class="container ">
        <div class="row justify-content-center">
            <div class="col-md-8">
                <div class="card">
                    <div class="card-header">
                      <h5 class="card-title">{{
                          $t('transfer.value') + ' - ' + $t(disableForm ? 'verification' : 'edit')
                        }}</h5>
                    </div>

                    <div class="card-body">
                        <form :aria-label="$t('transfer.coupon_form')" @submit.prevent="handleSubmit">
                            <loading v-if="isLoading"/>
                            <div class="row mb-3">
                                <label class="col-md-4 col-form-label text-md-end" for="receiverId">
                                    {{ $t('receiver') }} </label>
                                <div class="col-md-6">
                                    <input id="receiverId" v-model.number.trim="receiverId"
                                           :class="[{ 'is-invalid': errors.receiver_id  },
                                                disableForm ? 'form-control-plaintext' : 'form-control',
                                           ]" :readonly="disableForm"
                                           class="form-control " required
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
                                         :class="[{ 'is-invalid': errors[meal] ||errors.total_meals},
                                           disableForm ? 'form-control-plaintext' : 'form-control',
                                           ]"
                                           :max="couponOwner[meal]" :name="meal"
                                         :disabled="!receiverId" :readonly="disableForm "
                                         class="form-control " min="0" required
                                           type="number" value="0">
                                  <div v-for=" error in errors[meal] || errors.total_meals" class="invalid-feedback"
                                       role="alert">
                                        {{ $t(error) }}
                                    </div>
                                </div>
                            </div>
                            <div class="row mb-0">
                                <div class="col-md-8 offset-md-4">
                                    <button v-if="disableForm" class="btn btn-primary" type="submit">{{
                                            $t('submit')
                                        }}
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
        couponOwner: Object
    },
    data() {
        return {
            result: {
                message: this.$t('ready'),
              success: false,
                hide: true,
                errors: []
            },
          receiverId: '',
            mealQuantities: {}, // Object to store meal quantities
            errors: {},
            isLoading: false,
            disableForm: false,
        };
    },
    computed: {
        mealPlanPeriods: function () {
            return Object.keys(this.$enums.MealPlanPeriodEnum);
        },//Object.values(\App\Enum\MealPlanPeriodEnum::names()), // Get meal plan periods as array
        url: () => route('coupons.transfer.store'),
    },
    methods: {
        async transferCoupons(data) {
            axios.post(this.url, data).then(responseJson => {
                let json = responseJson.data;
                if (json.success) {
                    this.result.message = this.$t('transfer.successful');
                    for (let meal in this.mealQuantities)
                        this.couponOwner[meal] -= data[meal];

                } else {
                    this.result.message = this.$t("transfer.unsuccessful");
                    this.result.errors = json;
                }
            }).catch(errors => {
              if (errors.response && errors.response.status === 422)
                this.errors = errors.response.data.errors;
              else
                this.result.errors = errors;
                this.result.message = this.$t('required_failed');
            }).finally(() => {
                this.isLoading = false;
                this.disableForm = false;
            });

        },
        async handleSubmit() {
            const data = {
                receiver_id: this.receiverId,
                ...this.mealQuantities,
            };

            try {
                await this.transferCoupons(data);
                // Handle successful transfer (e.g., display success message, reset form)
                /*this.receiverId = '';
                this.mealQuantities = {};*/
            } catch (error) {
                // Handle errors appropriately (e.g., display error message)
                console.error('Error transferring coupons:', error);
            }
        },
      validateMeals(newValue, oldValue, key) {
        this.result.success = true;
        if (newValue > this.couponOwner[key]) {
          this.errors[key] = [this.$t('errors.maxCoupons', {'max': this.couponOwner[key]})]
        } else if (newValue < 0 || newValue === '') {
          this.errors[key] = [this.$t('errors.minCoupons', {'min': 0})]
        } else if (newValue === 0 && (Object.keys(this.mealQuantities).reduce((sum, key) => sum + this.mealQuantities[key], 0) === 0)) {
          this.errors.total_meals = [this.$t('errors.at_least_one_greater_than_zero')];
        } else {
          delete this.errors.total_meals;
          delete this.errors[key];
        }
      },
        validateForm() {
            this.errors = {};

          this.disableForm = this.result.success
        },
    },
  watch: {
    receiverId(newValue) {
      delete this.errors.receiver_id;
      if (newValue === this.couponOwner.academic_id) {
        // Vue.set(this.errors,'receiver_id',[this.$t('errors.transfer.myself')])
        this.errors.receiver_id = [this.$t('errors.transfer.myself')];
      } else if (newValue < 1) {
        this.errors.receiver_id = [this.$t('provide_valid_card')];
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

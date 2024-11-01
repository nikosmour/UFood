<template>
    <v-container :class="customClass" fluid>
        <v-row justify="center">
            <v-col cols="12" md="8">
                <v-stepper v-if="this.couponOwner" v-model="step"
                           :items="[$t('receiver'), $t('confirmation'), $t('transaction.summary')]" hide-actions>
                    <template v-slot:item.1>
                        <v-card :loading="isLoading" :title="$t('transaction.info')" flat>
                            <v-form ref="receiverForm" v-model="isFormValid" validate-on="invalid-input lazy"
                                    @submit.prevent="confirmDataTransactions">
                                <v-row>
                                    <v-col class="mt-10" cols="12" md="8" offset-md="2">
                                        <v-text-field
                                            v-model.number="receiver.id"
                                            :error-messages="errors.receiver_id"
                                            :label="$t('receiver')"
                                            :rules="rules.receiver"
                                            autofocus
                                            outlined
                                            required
                                            type="number"
                                            validate-on-blur
                                            @input="errors.receiver_id=null"
                                            density="compact"
                                        ></v-text-field>
                                    </v-col>
                                    <v-col v-for="(meal, index) in mealPlanPeriods" :key="index" cols="12" md="8"
                                           offset-md="2">
                                        <v-text-field
                                            v-model.number="mealQuantities[meal]"
                                            :error-messages="errors[meal] || errors.meals"
                                            :label="$t('meals.'+meal.toLowerCase())"
                                            :max="couponOwner[meal] ?? null"
                                            :rules="rules['meals'][meal]"
                                            min="0"
                                            outlined
                                            type="number"
                                            @input="errors[meal]=errors['meals']=null"
                                            density="compact"
                                        ></v-text-field>
                                    </v-col>
                                    <v-col class="d-flex justify-end" cols="12">
                                        <v-btn :disabled="!isFormValid" color="primary" type="submit">
                                            {{ $t('next') }}
                                        </v-btn>
                                    </v-col>
                                </v-row>
                            </v-form>
                        </v-card>
                    </template>
                    <template v-slot:item.2>
                        <v-card :loading="isLoading" :title="$t('transaction.info')">
                            <v-card-text>
                                <showListItem :list-items="listItems"/>
                            </v-card-text>

                            <v-card-actions class="d-flex justify-space-between" cols="12">
                                <v-btn color="primary" variant="text" @click="step--">{{ $t('previous') }}</v-btn>
                                <v-btn color="primary" type='submit' variant="elevated" @click="handleSubmit">{{
                                        $t('confirm')
                                    }}
                                </v-btn>
                            </v-card-actions>
                        </v-card>
                    </template>
                    <template v-slot:item.3>
                        <v-card :title="$t('transaction.info')">
                            <v-card-text>
                                <showListItem :list-items="listItems"/>
                            </v-card-text>

                            <v-card-actions class="justify-center">
                                <v-btn color="primary"
                                       variant="elevated" @click="resetForm">
                                    {{ $t('transaction.new') }}

                                </v-btn>
                            </v-card-actions>
                        </v-card>
                    </template>
                </v-stepper>
            </v-col>
        </v-row>
    </v-container>
</template>

<script>
import ShowListItem from "./ShowListItem.vue";

export default {
    components: {ShowListItem},
    props: {
        customClass: {
            type: [Array, Object, String],
            default: ''
        },
        couponOwner: {
            type: Object,
            default: () => ({})
        },
        transaction: String
    },
    data() {
        return {
            step: 1,
            receiver: {transaction: null, id: '', name: null, status: null},
            mealQuantities: {}, // Object to store meal quantities
            errors: {
                meals: null,
            },
            isFormValid: true,
            isLoading: false,
            url: this.route(`coupons.${this.transaction}.store`),
            rules: {
                receiver: [
                    value => {
                        if (value) return true;
                        return this.$t('validation.required', {'attribute': this.$t('receiver')});
                    },
                    value => {
                        if (value > 0) return true

                        return this.$t('validation.exists', {'attribute': this.$t('validation.attributes.receiver_id')});
                    },
                    value => {
                        if (!this.couponOwner || value !== this.couponOwner.academic_id) return true

                        return this.$t('errors.transfer.myself')
                    },
                    // this.confirmDataTransactions
                ],
                meals: {},
            }
        };
    },
    computed: {
        listItems() {
            return {
                receiver: this.receiver,
                meals: this.mealQuantities
            }
        },
        mealPlanPeriods() {
            return Object.keys(this.$enums.MealPlanPeriodEnum);
        }
    },
    methods: {
        handleSubmit() {
            const data = {
                receiver_id: this.receiver.id,
                ...this.mealQuantities,
            };
            this.isLoading = true;
            this.$axios.post(this.url, data).then(responseJson => {
                let json = responseJson.data;
                this.receiver.name = json.receiver;
                this.receiver.transaction = json.transaction;
                // this.result.message = this.$t(`${this.transaction}.successful`);
                this.$emit(`new_${this.transaction}`, this.mealQuantities);
                this.step = 3;


            }).catch(errors => {
                if (errors.response && errors.response.status === 422)
                    this.errors = errors.response.data.errors;
                /*else
                    this.result.errors = errors;*/
                // this.result.message = this.$t('request_failed');
                this.step = 1;
            }).finally(() => {
                this.isLoading = false;
            });
        },
        validateMeals(key) {
            return newValue => {
                if (this.couponOwner && newValue > this.couponOwner[key]) {
                    return this.$t('validation.max.numeric', {
                        'max': this.couponOwner[key],
                        'attribute': this.$t(`meals.${key}`)
                    });
                }
                if (newValue < 0) {
                    return this.$t('validation.min.numeric', {
                        'min': 0,
                        'attribute': this.$t(`meals.${key}`)
                    });
                }
                if (newValue === 0 && (Object.keys(this.mealQuantities).reduce((sum, key) => sum + this.mealQuantities[key], 0) === 0)) {
                    this.errors.meals = this.$t('validation.at_least_one_greater_than_zero', {
                        'attribute': this.$t(`meal`, 2).toLocaleLowerCase()
                    });
                    return true;
                }
                this.errors.meals = null;
                return true;
            }
        },
        async confirmDataTransactions() {
            await this.$refs.receiverForm.validate();
            if (!this.isFormValid) {
                return;
            }
            this.mealPlanPeriods.forEach((key) => {
                this.mealQuantities[key] = this.mealQuantities[key] || 0;
            });
            const data = {
                receiver_id: this.receiver.id,
                ...this.mealQuantities,
            };
            this.isLoading = true;
            this.$axios.post(this.route(`transaction.confirm`), data).then(responseJson => {
                console.log(responseJson)
                let json = responseJson.data;
                console.log(json)
                this.receiver.name = json.data.name;
                this.receiver.status = json.data.status;
                this.step = 2;
            }).catch(errors => {
                if (errors.response && errors.response.status === 422)
                    this.errors = errors.response.data.errors;
                else
                    console.log(errors);
            }).finally(() => {
                this.isLoading = false;
            });
        },
        resetForm() {
            this.$refs.receiverForm.reset();
            this.step = 1;
            this.receiver.transaction = null;
            this.receiver.name = null;
            this.receiver.status = null;
        }
    },
    created() {
        this.mealPlanPeriods.forEach((key) => {
            this.rules['meals'][key] = [this.validateMeals(key.toLocaleLowerCase())];
        });
    }
};
</script>


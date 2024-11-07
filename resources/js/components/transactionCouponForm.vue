<template>
    <v-container fluid>
        <v-row justify="center">
            <v-col cols="12">
                <v-stepper v-if="this.couponOwner" v-model="step"
                           :items="[$t('receiver.value'), $t('confirmation'), $t('transaction.summary')]" hide-actions>
                    <template v-slot:item.1>
                        <v-card :loading="isLoading" :title="$t('transaction.info')" flat>
                            <v-form ref="receiverForm" v-model="isFormValid" validate-on="invalid-input lazy"
                                    @submit.prevent="confirmDataTransactions">
                                <v-row>
                                    <v-col class="mt-10" cols="12" md="8" offset-md="2">
                                        <v-text-field
                                            :ref="'receiverId'"
                                            v-model.number="receiver.id"
                                            :error-messages="errors.receiver_id"
                                            :label="$t('receiver.value')"
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
                                    <v-col v-for="(meal, index) in mealPlanPeriods" :key="'form.meals.'+index" cols="12"
                                           md="8"
                                           offset-md="2">
                                        <v-text-field
                                            :ref="`meals-${meal}`"
                                            v-model.number="mealQuantities[meal]"
                                            :disabled="mealsDisable"
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
                                        <v-btn :disabled="isFormValid === false" color="primary" type="submit">
                                            {{ $t('next') }}
                                        </v-btn>
                                    </v-col>
                                </v-row>
                            </v-form>
                        </v-card>
                    </template>
                    <template v-slot:item.2>
                        <v-card :loading="isLoading" :title="$t('transaction.info')">
                            <v-card-text @keydown.enter="handleSubmit">
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
                            <v-card-text @keydown.enter="handleSubmit">
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
        couponOwner: {
            type: Object,
            default: () => ({})
        },
        transaction: String
    },
    data() {
        return {
            step: 1,
            receiver: {transaction_id: null, id: '', name: null, status: null},
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
                        return this.$t('validation.required', {'attribute': this.$t('receiver.value')});
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
    emits: [
        'new_transaction_coupon',
    ],
    computed: {
        listItems() {
            return {
                receiver: this.receiver,
                meals: this.mealQuantities
            }
        },
        mealPlanPeriods() {
            return Object.keys(this.$enums.MealPlanPeriodEnum);
        },
        mealsDisable() {
            return !this.receiver.id;
        },
    },
    methods: {
        focusOnError() {
            if (!this.$refs.receiverId.isValid) {
                this.$refs.receiverId.focus();
                return; // Exit after focusing on receiverId if it's invalid
            }
            // Loop through meal plan periods
            // Focus on the first invalid meal input
            for (const meal of this.mealPlanPeriods) {
                const mealRef = this.$refs[`meals-${meal}`][0];
                console.log(meal, mealRef, mealRef.isValid)
                if (mealRef && !mealRef.isValid) {
                    mealRef.focus();
                    return; // Exit after focusing on the first invalid input
                }
            }
        },
        async submitData(url, data) {
            this.isLoading = true;
            try {
                const responseData = (await this.$axios.post(url, data)).data;
                this.receiver.name = responseData.name;
                this.step++
                return responseData;
            } catch (error) {
                if (error.response && error.response.status === 422) {
                    this.errors = error.response.data.errors;
                } else {
                    throw error;
                }
                this.step = 1;
                this.$nextTick(this.focusOnError).then(r => {
                });
            } finally {
                this.isLoading = false;
            }
        },
        handleSubmit() {
            const data = {
                receiver_id: this.receiver.id,
                ...this.mealQuantities,
            };
            this.submitData(this.url, data).then(json => {
                this.receiver.transaction_id = json.transaction;
                this.$emit(`new_transaction_coupon`, this.mealQuantities);
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
            this.submitData(this.route(`transaction.confirm`), data).then(json => {
                this.receiver.status = json.status;
            });
        },
        resetForm() {
            this.$refs.receiverForm.reset();
            this.step = 1;
            this.receiver.transaction_id = null;
            this.receiver.name = null;
            this.receiver.status = null;
            this.$nextTick(() => {
                this.$refs.receiverId.focus();
            });
        },
    },
    created() {
        this.mealPlanPeriods.forEach((key) => {
            this.rules['meals'][key] = [this.validateMeals(key.toLocaleLowerCase())];
        });
    },
};
</script>


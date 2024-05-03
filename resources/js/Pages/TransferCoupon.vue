<template>
    <div>
        <form v-if="couponOwner" @submit.prevent="handleSubmit">
            <div class="form-group">
                <label for="receiverId">{{ "Receiver id" }}</label>
                <input id="receiverId" v-model="receiverId" class="form-control" min="1" name="receiverId" required
                       type="number"/>
            </div>

            <template v-for="(meal, index) in mealPlanPeriods" :key="index">
                <div class="form-group">
                    <label for="{{ meal }}">{{ meal }}</label>
                    <input :id="meal" v-model="mealQuantities[meal]" :max="couponOwner[meal]" :name="meal"
                           class="form-control"
                           min="0" required type="number"/>
                </div>
            </template>

            <button class="btn btn-primary" type="submit">{{ "Send" }}</button>
        </form>
        <message v-bind="result"></message>
    </div>
</template>

<script>
import {mapState} from 'vuex'; // Assuming Vuex is used for state management

export default {
    data() {
        return {
            result: {
                message: 'ready',
                success: true,
                hide: true,
                errors: []
            },
            receiverId: '',
            mealQuantities: {}, // Object to store meal quantities
        };
    },
    computed: {
        ...mapState({
            couponOwner: state => state.auth.user.coupon_owner, // Assuming couponOwner data is in Vuex state
        }),
        mealPlanPeriods: function () {
            return Object.keys(this.$enums.MealPlanPeriodEnum);
        },//Object.values(\App\Enum\MealPlanPeriodEnum::names()), // Get meal plan periods as array
        url: () => route('coupons.transfer.store'),
    },
    methods: {
        async transferCoupons(data) {
            axios.post(this.url, data
            ).then(responseJson => {
                let json = responseJson['data'];
                this.result.success = json['success'];
                if (json['success']) {
                    this.result.message = "Επιτυχής μεταφορά";
                    // vue.$emit('newPurchase', vue.form_data);
                    this.result.errors = [];
                } else {
                    this.result.message = "Request failed:";
                    this.result.errors = json;
                }
            }).catch(errors => {
                this.result.success = false;
                this.result.errors = errors.response.data.errors;
                console.log(errors.response.data.errors);
                this.result.message = "Request failed:";
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
                this.receiverId = '';
                this.mealQuantities = {};
            } catch (error) {
                // Handle errors appropriately (e.g., display error message)
                console.error('Error transferring coupons:', error);
            }
        },
    },
};
</script>

<style scoped>
/* Add any necessary styles here */
</style>

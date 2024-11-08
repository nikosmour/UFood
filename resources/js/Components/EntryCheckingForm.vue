<template>
    <v-container>
        <v-card :loading="isLoading" class="pa-5">
            <!--            <v-card-title class="mb-10">{{ $t("entry_check") }}</v-card-title>-->

            <v-card-text>
                <v-form aria-label="$t('entry_check_form')" @submit.prevent="check_id">
                    <v-text-field
                        v-model.number.trim="academic_id"
                        :error-messages="errors.academic_id"
                        :label="$t('id')"
                        max-width="50em"
                        outlined
                        required
                        type="number"
                    />
                    <!--                    :class="{'text-field&#45;&#45;error':result.success}"
                                        :style=" 'border-color:' + (result.success) ?  'green;' : 'red;'"-->
                </v-form>
                <v-alert
                    :aria-hidden="!show"
                    :class="{'opacity-100' :show,'opacity-0':!show,'mt-4':true}"
                    :title="result.message "
                    :type="result.success ? 'success' : 'error'"
                    dismissible
                    @input="show = false"
                />
            </v-card-text>
            <v-card-actions class="justify-center">
                <v-btn :loading="isLoading" color="primary" @click="check_id">
                    {{ $t("submit") }}
                </v-btn>
            </v-card-actions>


        </v-card>
    </v-container>
</template>

<script>
export default {
    data() {
        return {
            academic_id: '',
            url: this.route('entryChecking.store'),
            result: {
                message: this.$t("test.message"),
                success: true,
                hide: true,
                errors: ['']
            },
            errors: {},
            isLoading: false,
            show: false,
            time: 2500
        };
    },
    methods: {
        check_id() {
            if (0 === this.academic_id)
                return;
            let params = new FormData();
            params.append('academic_id', this.academic_id);
            this.result.message = '';
            this.isLoading = true;
            this.errors = {};
            this.$axios.post(this.url, params)
                .then(response => {
                    let json = response.data;
                    this.result.success = json.success;
                    if (json.success) {
                        this.result.message = json.passWith;
                        this.$emit('newEntry', json.passWith + 's');
                        this.errors = {};
                    } else {
                        this.result.message = this.$t("request_failed");
                        this.errors = {academic_id: [json.card.message, json.coupon.message]};
                    }
                    this.show = true;
                })
                .catch(error => {
                    this.result.success = false
                    this.show = true
                    this.result.message = this.$t("request_failed");
                    if (error.response && error.response.status === 422)
                        this.errors = error.response.data.errors;
                    else
                        throw error
                })
                .finally(() => {
                    this.isLoading = false;
                    setTimeout(() => {
                        this.show = false;
                        this.errors = {};
                    }, this.time);
                });
        }
    }
};
</script>

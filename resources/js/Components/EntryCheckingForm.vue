<template>
    <div class="container col-12 col-sm-6 col-md-7 col-lg-8 h-100">
        <div class="row justify-content-center h-100">
            <div class="col-md-8 d-flex flex-column h-100">
                <div class="card  flex-grow-1">
                    <div class="card-header">
                        <h5 class="card-title">{{ $t('entry_check') }}</h5>
                    </div>

                    <div class="card-body ">
                        <form :aria-label="$t('entry_check_form')" @submit.prevent="check_id">
                            <div class="row mb-3">
                                <label class="col-md-4 col-form-label text-md-end" for="academic_id">
                                    {{ $t('entry_input') }}</label>
                                <div class="col-md-6">
                                    <input id="academic_id" v-model.number.trim="academic_id"
                                           :class="{ 'is-invalid': errors.academic_id  }"
                                           class="form-control " required
                                           type="number">
                                    <div v-for=" error in errors.academic_id" class="invalid-feedback" role="alert">
                                        {{ $t(error) }}
                                    </div>
                                </div>
                            </div>
                            <loading v-if="isLoading"/>
                        </form>
                        <div v-if="result.success && show" class="alert alert-success mt-3" role="alert">
                            {{ $t(result.message) }}
                        </div>

                    </div>
                </div>
            </div>
        </div>
    </div>
</template>

<script>
import axios from "axios";

export default {
    data() {
        return {
            academic_id: '',
            url: route('entryChecking.store'),
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
            axios.post(this.url, params)
                .then(response => {
                    let json = response.data;
                    this.result.success = json.success;
                    if (json.success) {
                        this.result.message = json.passWith;
                        this.$emit('newEntry', json.passWith + 's');
                        this.errors = [];
                    } else {
                        this.result.message = this.$t('request_failed');
                        this.errors = {'academic_id': [json['card']['message'], json['coupon']['message']]};
                    }
                    this.show = true;
                })
                .catch(errors => {
                    this.result.success = false;
                    this.errors = errors.response.data.errors;
                    this.result.message = this.$t('request_failed');
                }).finally(() => {
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

<template>
    <div class="row">
        <button class="btn btn-link w-100 text-start" @click="showApplicant=!showApplicant">
            {{ $t('model_data.applicant_info') }}
            <span v-if="showApplicant">&#9650;</span>
            <span v-else>&#9660;</span>
        </button>
        <template v-if="showApplicant">
            <h3>{{ $t('model_data.applicant_info') }}</h3>
            <show-a-data :name="$t('model_data.academic_id')"
                         :value=" model['academic_id']" class="col-auto row"/>
            <show-a-data :name="$t('model_data.a_m')"
                         :value=" model['academic']['a_m']" class="col-auto row"/>
            <show-a-data :name="$t('model_data.name')"
                         :value=" model['academic']['name']" class="col-auto row"/>
            <show-a-data :name="$t('model_data.email')"
                         :value=" model['academic']['email']" class="col-auto row"/>
            <show-a-data :name="$t('model_data.status')"
                         :value=" model['academic']['status']" class="col-auto row"/>
            <show-a-data :name="$t('model_data.is_active')"
                         :value=" model['academic']['is_active']" class="col-auto row"/>
            <show-a-data :name="$t('model_data.department')"
                         :value=" model['card_applicant']['department']['name']" class="col-auto row"/>
            <show-a-data :name="$t('model_data.first_year')"
                         :value=" model['card_applicant']['first_year']" class="col-auto row"/>
            <show-a-data :name="$t('model_data.cellphone')"
                         :value=" model['card_applicant']['cellphone']" class="col-auto row"/>

            <template v-for="address in model['addresses']">
                <h5>{{ $t((address['is_permanent']) ? 'address.permanent' : 'address.temporary') }}</h5>
                <show-a-data :name="$t('model_data.address')"
                             :value=" address['location']" class="col-auto row"/>
                <show-a-data :name="$t('model_data.phone')"
                             :value=" address['phone']" class="col-auto row"/>
            </template>
        </template>

    </div>
</template>

<script>
export default {
    props: {
        model: {
            type: Object,
            required: false,
            default: () => {
            },
        },
    },
    data() {
        return {
            showApplicant: true,
        };
    },
};
</script>

<style scoped>
</style>

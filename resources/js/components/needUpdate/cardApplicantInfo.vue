<template>
    <div class = "row">
        <button class = "btn btn-link w-100 text-start" @click = "showApplicant=!showApplicant">
            {{ $t( "model_data.applicant_info" ) }}
            <span v-if = "showApplicant">&#9650;</span>
            <span v-else>&#9660;</span>
        </button>
        <template v-if = "showApplicant">
            <h3>{{ $t( "model_data.applicant_info" ) }}</h3>
            <show-a-data
                :name = "$t('model_data.academic_id')"
                :value = " applicant['academic_id']" class = "col-auto row"
            />
            <show-a-data
                :name = "$t('model_data.a_m')"
                :value = " applicant['a_m']"
            />
            <show-a-data
                :name = "$t('model_data.name')"
                :value = " applicant['name']" class = "col-auto row"
            />
            <show-a-data
                :name = "$t('model_data.email')"
                :value = " applicant['email']" class = "col-auto row"
            />
            <show-a-data
                :name = "$t('model_data.status')"
                :value = " applicant['status']" class = "col-auto row"
            />
            <show-a-data
                :name = "$t('model_data.is_active')"
                :value = " applicant['is_active']" class = "col-auto row"
            />
            <show-a-data
                :name = "$t('model_data.department')"
                :value = " applicant['card_applicant']['department']" class = "col-auto row"
            />
            <show-a-data
                :name = "$t('model_data.first_year')"
                :value = " applicant['card_applicant']['first_year']" class = "col-auto row"
            />
            <show-a-data
                :name = "$t('model_data.cellphone')"
                :value = " applicant['card_applicant']['cellphone']" class = "col-auto row"
            />

            <template v-for = "address in applicant['card_applicant']['addresses']">
                <h5>{{ $t( ( address[ "is_permanent" ] )
                           ? "address.permanent"
                           : "address.temporary" ) }}</h5>
                <show-a-data
                    :name = "$t('model_data.address')"
                    :value = " address['location']" class = "col-auto row"
                />
                <show-a-data
                    :name = "$t('model_data.phone')"
                    :value = " address['phone']" class = "col-auto row"
                />
            </template>
        </template>

    </div>
</template>

<script>
import ShowAData from "@components/needUpdate/showAData.vue";

export default {
	components : { ShowAData },
	props : {
		applicant : {
			type :     Object,
			required : true,
		},
	},
	data() {
		return {
			showApplicant : false,
		};
	},
};
</script>

<style scoped>
</style>

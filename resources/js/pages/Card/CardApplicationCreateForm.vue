<template>
    <v-card :loading = "isLoading" class = "justify-content-center">
        <!-- Alert Message -->
        <v-alert
            aria-live = "assertive"
            closable
            role = "alert"
            style = "white-space: pre-wrap"
            type = "info"
        >
            {{ alert_info }}
        </v-alert>

        <!-- Form Section -->
        <v-form
            v-if = "!isFetching && user.card_applicant"
            id = "accept-form"
            :aria-label = "$t('personalInfo')"
            class = "mt-5"
            @submit.prevent = "createApplication"
        >
            <card-applicant-info
                :user = "user"
            />

            <!-- Action Buttons -->
            <v-row class = "d-flex justify-space-between mx-2 my-2">
                <v-btn
                    v-tooltip.top = "$t('applicant-update-info')"
                    :aria-label = "$t('applicant-update-info')"
                    :loading = "isLoading"
                    :text = "$t('update')"
                    color = "secondary"
                    @click = "retrieveApplicant(true)"
                />

                <v-btn
                    v-tooltip.bottom = "$t('applicant-accept-info')"
                    :aria-label = "$t('applicant-accept-info')"
                    :loading = "isLoading"
                    :text = "$t('accept')"
                    color = "primary"
                    type = "submit"
                    variant = "elevated"
                    @click = "createApplication"
                />
            </v-row>
        </v-form>
    </v-card>
</template>

<script lang = "ts">
import { mapGetters } from "vuex";
import { InformTheUserError } from "@/errors/InformTheUserError";
import CardApplicant from "@models/CardApplicant";
import cardApplicantInfo from "@components/needUpdate/cardApplicantInfo.vue";

export default {
	emits :      [ "created" ],
	components : {
		cardApplicantInfo,
	},
	data() {
		return {
			url :          this.route( "cardApplication.store" ),
			isFetching :   false,
			isSubmitting : false,
			isNewApplicant : true,

		};
	},

	computed : {
		...mapGetters( "auth", { user : "currentUser" } ),

		isLoading() : boolean {
			return this.isFetching || this.isSubmitting;
		},

		alert_info() : string {
			return this.isNewApplicant
			       ? this.$t( "applicantInfoFormMessage.update" )
			       : this.$t( "applicantInfoFormMessage.create" );
		},
	},

	methods : {
		async createApplication() {
			if ( this.isLoading ) return;

			if ( !this.user.academic_id ) {
				throw new InformTheUserError( { message : "noAcademicIDApplication" } );
			}

			try {
				this.isSubmitting = true;
				await CardApplicant.create( this.user );
				this.$emit( "created" );
			} catch ( errors ) {
				if ( errors.response?.status === 422 ) {
					this.errors = errors.response.data.errors;
				} else
					throw errors;
			} finally {
				this.isSubmitting = false;
			}
		},

		async retrieveApplicant( byTheSystem = false ) {
			if ( this.isFetching ) return;

			try {
				this.isFetching = true;
				await this.user.prepareForApplicationCreate( byTheSystem );
			} finally {
				this.isNewApplicant = !!this.user?.card_applicant?.academic_id;
				this.isFetching = false;
			}
		},


	},

	created() {
		this.retrieveApplicant()
	},
};
</script>

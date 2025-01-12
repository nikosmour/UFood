<template>
    <v-card :loading = "isLoading" class = "justify-content-center">
        <v-alert
            aria-live = "assertive"
            closable
            role = "alert"
            style = "white-space: pre-wrap"
            type = "info"
        >
            {{ alert_info }}
        </v-alert>

        <v-form
            v-if = "!isFetching && user.card_applicant"
            id = "accept-form" :aria-label = "$t('personalInfo')" class = "mt-5" @submit.prevent = "createApplication"
        >
            <v-row>
                <v-col cols = "12" md = "6">
                    <v-text-field
                        v-model = "user.name"
                        :readonly = "!user.shouldBeTracked('name')"
                        :variant = " (!user.shouldBeTracked('name')) ? 'plain' :'outlined'"
                        :error-messages = "errors.name"
                        :label = "$t('model_data.name')"
                        required
                        type = "text"
                    />
                </v-col>
                <v-col cols = "12" md = "6">
                    <v-text-field
                        v-model = "first_year"
                        :readonly = "!user.card_applicant.shouldBeTracked('first_year')"
                        :type = " (!user.card_applicant.shouldBeTracked('first_year')) ? 'text' :'number'"
                        :error-messages = "errors.first_year"
                        :label = "$t('model_data.first_year')"
                        required
                        :variant = " (!user.card_applicant.shouldBeTracked('first_year')) ? 'plain' :'outlined'"
                    />
                </v-col>
                <v-col cols = "12" md = "6">
                    <v-text-field
                        v-model = "user.status.value"
                        :readonly = "!user.shouldBeTracked('status')"
                        :variant = " (!user.shouldBeTracked('status')) ? 'plain' :'outlined'"
                        :error-messages = "errors.status"
                        :label = "$t('model_data.status')"
                        required
                        type = "text"
                    />
                </v-col>
                <v-col cols = "12" md = "6">
                    <v-text-field
                        v-model = "user.card_applicant.department"
                        :readonly = "!user.card_applicant.shouldBeTracked('department')"
                        :variant = " (!user.card_applicant.shouldBeTracked('department')) ? 'plain' :'outlined'"
                        :error-messages = "errors.department"
                        :label = "$t('model_data.department')"
                        required
                        type = "text"
                    />
                </v-col>
                <v-col cols = "12" md = "6">
                    <v-text-field
                        v-model = "user.a_m"
                        :readonly = "!user.shouldBeTracked('a_m')"
                        :variant = " (!user.shouldBeTracked('a_m')) ? 'plain' :'outlined'"
                        :error-messages = "errors.a_m"
                        :label = "$t('model_data.a_m')"
                        required
                        type = "text"
                    />
                </v-col>
                <v-col cols = "12" md = "6">
                    <v-text-field
                        v-model = "user.academic_id"
                        :readonly = "!user.shouldBeTracked('academic_id')"
                        :variant = " (!user.shouldBeTracked('academic_id')) ? 'plain' :'outlined'"
                        :error-messages = "errors.academic_id"
                        :label = "$t('model_data.academic_id')"
                        required
                        type = "text"
                    />
                </v-col>

            </v-row>
            <v-row v-for = "type in ['permanent','temporary']" :key = "type">
                <v-col cols = "12" md = "6">
                    <v-text-field
                        v-model = "addresses[type].location"
                        :readonly = "!addresses[type].shouldBeTracked('location')"
                        :variant = " (!addresses[type].shouldBeTracked('location')) ? 'plain' :'outlined'"
                        :error-messages = "errors[`addresses.${type}.location`]"

                        :label = "$t('address.'+type)"
                        :required = "type==='temporary'"
                        type = "text"
                    />
                </v-col>
                <v-col cols = "12" md = "6">
                    <v-text-field
                        v-model = "addresses[type].phone"
                        :readonly = "!addresses[type].shouldBeTracked('phone')"
                        :variant = " (!addresses[type].shouldBeTracked('phone')) ? 'plain' :'outlined'"
                        :error-messages = "errors[`addresses.${type}.phone`]"
                        :label = "$t('address.phone.'+type)"
                        :required = "type==='temporary'"
                        type = "text"
                    />
                </v-col>
            </v-row>
            <v-row class = "d-flex justify-space-between mr-2 ml-2 mt-2 mb-2">
                <v-btn
                    v-tooltip.top = "$t('applicant-update-info')" :aria-label = " $t('applicant-update-info')"
                    :text = "$t( 'update' )" color = "secondary" @click = "retrieveApplicant(true)"
                    :loading = "isLoading"
                />
                <v-btn
                    v-tooltip.bottom = "$t('applicant-accept-info')" :aria-label = " $t('applicant-accept-info')"
                    :text = "$t( 'accept' )" color = "primary" @click = "createApplication"
                    :loading = "isLoading" type = "sumbit"
                    variant = "elevated"

                />
            </v-row>
        </v-form>
    </v-card>
</template>


<script lang = "ts">
import { mapGetters } from "vuex";
import CardApplicantInfo from "@components/needUpdate/cardApplicantInfo.vue";
import { InformTheUserError } from "@/errors/InformTheUserError";
import Address from "@models/Address";
import CardApplicant from "@models/CardApplicant";

export default {
	components : { CardApplicantInfo },
	emits : [
		"created",
	],
	data : function () {
		return {
			url :          this.route( "cardApplication.store" ),
			errors :       {
				"permanent" : {},
				"temporary" : {},
			},
			isFetching : false,
			isSubmitting : false,
			isNewApplicant : true,
			first_year : "",
		};
	},

	computed : {
		...mapGetters( "auth", {
			user : "currentUser",
		} ),

		isLoading() : boolean {
			return this.isFetching || this.isSubmitting;
		},

		alert_info() : string {
			if ( !this.isNewApplicant ) {
				return this.$t( "applicantInfoFormMessage.create" );
			}

			return this.$t( "applicantInfoFormMessage.update" );

		},
		addresses() : Record<string, Address> {
			return ( this.user.card_applicant?.addresses as Address[] ).reduce(
				( o, input ) => {
					const type = input.is_permanent
					             ? "permanent"
					             : "temporary";
					o[ type ] = input;
					return o;
				},
				{} as Record<string, Address>,
			);
		},
	},
	methods :  {
		async createApplication() {
			if ( this.isLoading )
				return;
			if ( !this.user.academic_id )
				throw new InformTheUserError( {
					                              message : "noAcademicIDApplication",
				                              } );
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
		async retrieveApplicant( byTheSystem : boolean = false ) {
			if ( this.isFetching ) return;
			try {
				this.isFetching = true;
				await this.user.prepareForApplicationCreate( byTheSystem );
				this.first_year = this.user.card_applicant?.first_year?.getFullYear() ?? "";
			} catch ( error ) {
				throw error;
			} finally {
				this.isNewApplicant = this.user?.card_applicant?.academic_id;
				this.isFetching = false;
			}

		},
	},
	created() {
		this.retrieveApplicant();
		const card_applicant = this.user.card_applicant;
		if ( card_applicant.shouldBeTracked( "first_year" ) )
			this.$watch( "first_year", ( newValue ) => {
				card_applicant.first_year = newValue;
			} );

	},
};
</script>

<style>
</style>

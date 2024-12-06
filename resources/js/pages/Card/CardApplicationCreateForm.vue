<template>
    <v-card :loading = "!user" class = "justify-content-center">
        <card-applicant-info :applicant = "user" :aria-label = "$t('personalInfo')" :caption = "$t('personalInfo')" />
        <v-form id = "accept-form" aria-label = "Accept Form" class = "mt-5" @submit.prevent = "createApplication">
            <v-btn
                :aria-label = " $t('accept')" :text = "$t( 'accept' )" color = "primary" @click = "createApplication"
            />
        </v-form>
    </v-card>
</template>


<script lang = "ts">
import { mapGetters } from "vuex";
import CardApplicantInfo from "@/Components/cardApplicantInfo.vue";
import { InformTheUserError } from "@/errors/InformTheUserError";
import type Academic from "@models/Academic";

export default {
	components : { CardApplicantInfo },
	data : function () {
		return {
			url :    this.route( "cardApplication.store" ),
		};
	},

	computed : {
		...mapGetters( "auth", {
			user : "currentUser",
		} ),
	},
	methods :  {
		createApplication() {
			if ( !this.user.academic_id )
				throw new InformTheUserError( {
					                              message : "noAcademicIDApplication",
				                              } );
			this.$axios.post( this.url )
			    .then( responseJson => {
				    // possible impact #future (Issue #001) Load Documents from Previous Application
				    responseJson.data.card_application_document ??= [];
				    ( this.user as Academic ).card_applicant.current_card_application = responseJson.data;
			    } )
			    .catch( () => {
				    throw new InformTheUserError();
			    } );
		},
	},
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

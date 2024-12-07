<template>
    <v-card :loading = "!user" class = "justify-content-center">
        <card-applicant-info :applicant = "user" :aria-label = "$t('personalInfo')" :caption = "$t('personalInfo')" />
        <v-form id = "accept-form" aria-label = "Accept Form" class = "mt-5" @submit.prevent = "createApplication">
            <v-row>
                <v-col v-for = "(input, index) in inputs" :key = "index" cols = "12" md = "6">
                    <v-text-field
                        :label = "$t('model_data.'+input.label)"
                        :value = "getNestedValue(user, input.path)"
                        required
                        type = "text"
                        @input = "setNestedValue(user, input.path, $event)"
                    />
                </v-col>
            </v-row>
            <v-row v-for = "type in ['permanent','temporary']" :key = "type">
                <v-col cols = "12" md = "6">
                    <v-text-field
                        v-model = "addresses[type].location"
                        :label = "$t('address.'+type)"
                        required
                        type = "text"
                    />
                </v-col>
                <v-col cols = "12" md = "6">
                    <v-text-field
                        v-model = "addresses[type].phone"
                        :label = "$t('address.phone.'+type)"
                        required
                        type = "text"
                    />
                </v-col>
            </v-row>
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
import Address from "@models/Address";

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
		inputs() {
			return [
				{
					label : "name",
					path :  "name",
				},
				{
					label : "first_year",
					path :  "card_applicant.first_year",
				},
				{
					label : "status",
					path :  "status",
				},
				{
					label : "department",
					path :  "card_applicant.department",
				},
				{
					label : "a_m",
					path :  "a_m",
				},
				{
					label : "academic_id",
					path :  "academic_id",
				},
			];
		},
		addresses() : Record<string, Address> {
			const addresses = ( this.user.card_applicant.addresses as Address[] ).reduce(
				( o, input ) => {
					const type = input.is_permanent
					             ? "permanent"
					             : "temporary";
					o[ type ] = input;
					return o;
				},
				{} as Record<string, Address>,
			);

			// Ensure "permanent" and "temporary" addresses exist
			addresses[ "permanent" ] ??= new Address( { is_permanent : true } );
			addresses[ "temporary" ] ??= new Address( { is_permanent : false } );

			return addresses;
		},
	},
	methods :  {
		async createApplication() {
			if ( !this.user.academic_id )
				throw new InformTheUserError( {
					                              message : "noAcademicIDApplication",
				                              } );
			try {
				const responseJson = await this.$axios.post( this.url );
				responseJson.data.card_application_document ??= [];
				( this.user as Academic ).card_applicant.current_card_application = responseJson.data;
			} catch ( error ) {
				throw error;
			}
		},
		getNestedValue( obj : object, path : string ) {
			return path.split( "." )
			           .reduce( ( o, key ) => ( o && o[ key ] !== undefined
			                                    ? o[ key ]
			                                    : "" ), obj );
		},
		setNestedValue( obj : object, path : string, value : string | number ) {
			const keys = path.split( "." );
			const lastKey = keys.pop();
			const target = keys.reduce( ( o, key ) => ( o && o[ key ] !== undefined
			                                            ? o[ key ]
			                                            : ( o[ key ] = {} ) ), obj );
			if ( target && lastKey ) target[ lastKey ] = value;
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

<template>
    <div>
        <v-row>
            <!-- User Info Fields -->
            <v-col cols = "12" md = "6">
                <v-text-field
                    v-model = "user.name"
                    v-bind = "getFieldProps('name', user)"
                />
            </v-col>

            <v-col cols = "12" md = "6">
                <v-text-field
                    v-model = "user.email"
                    v-bind = "getFieldProps('email', user, 'text')"
                />
            </v-col>

            <v-col cols = "12" md = "6">
                <v-text-field
                    :model-value = "$t('status.'+user.status.key)"
                    v-bind = "getFieldProps('status', user)"
                />
            </v-col>

            <v-col v-if = "user.card_applicant" cols = "12" md = "6">
                <v-text-field
                    v-model = "user.card_applicant.department"
                    v-bind = "getFieldProps('department', user.card_applicant)"
                />
            </v-col>
            <template v-if = "isAcademic">
            <v-col cols = "12" md = "6">
                <v-text-field
                    v-model = "user.a_m"
                    v-bind = "getFieldProps('a_m', user)"
                />
            </v-col>

            <v-col cols = "12" md = "6">
                <v-text-field
                    v-model = "user.academic_id"
                    v-bind = "getFieldProps('academic_id', user)"
                />
            </v-col>

                <v-col cols = "12" md = "6">
                    <v-text-field
                        :model-value = "$t( 'active', user.is_active ? 1: 0 ) "
                        v-bind = "getFieldProps('is_active', user, 'number')"
                    />
                </v-col>

                <v-col v-if = "user.card_applicant" cols = "12" md = "6">
                    <v-text-field
                        v-model = "first_year"
                        v-bind = "getFieldProps('first_year', user.card_applicant, 'number')"
                    />
                </v-col>
            </template>
        </v-row>

        <!-- Address Fields -->
        <v-row v-for = "type in ['permanent', 'temporary']" :key = "type">
            <v-col cols = "12" lg = "6" md = "7">
                <v-text-field
                    v-model = "addresses[type].location"
                    :error-messages = "errors['addresses.'+type+'.location'] "
                    v-bind = "getFieldProps('location', addresses[type])"
                    :label = "$t('address.' + type)"
                    :required = "type === 'temporary'"
                />
            </v-col>

            <v-col cols = "12" lg = "6" md = "5">
                <v-text-field
                    v-model = "addresses[type].phone"
                    :error-messages = "errors['addresses.'+type+'.phone']"
                    v-bind = "getFieldProps('phone', addresses[type])"
                    :label = "$t('address.phone.' + type)"
                    :required = "type === 'temporary'"
                />
            </v-col>
        </v-row>
    </div>
</template>

<script lang = "ts">
import Address from "@models/Address";
import Academic from "@models/Academic";
import BaseModel from "@utilities/BaseModel";
import { defineComponent } from "vue";
import cardApplicant from "@models/CardApplicant";

export default defineComponent( {
	                                name :  "cardApplicantInfo",
	                                props : {
		                                user :   {
			                                type : BaseModel<any, any>,
			                                required : true,
		                                },
		                                errors : {
			                                type :     Object,
			                                required : false,
			                                default :  {},
		                                },
		                                canUpdate : {
			                                type :    Boolean,
			                                default : false,
		                                },
	                                },
	                                data() {
		                                return {
			                                showApplicant : false,
			                                isAcademic : this.user instanceof Academic,
		                                };
	                                },
	                                computed : {
		                                cardApplicant() {
			                                return cardApplicant;
		                                },
		                                addresses() : Record<string, Address> {
			                                return ( this.user.card_applicant?.addresses as Address[] ).reduce(
				                                ( acc, input ) => {
					                                const type = input.is_permanent
					                                             ? "permanent"
					                                             : "temporary";
					                                acc[ type ] = input;
					                                return acc;
				                                },
				                                {} as Record<string, Address>,
			                                );
		                                },
		                                first_year() {
			                                return this.user.card_applicant?.first_year?.getFullYear() ?? "";
		                                },

	                                },
	                                methods :  {
		                                getFieldProps( fieldName : string, model : any, type : string = "text" ) {
			                                const shouldTrack = this.canUpdate && model.shouldBeTracked( fieldName );
			                                return {
				                                readonly :         !shouldTrack,
				                                variant :          shouldTrack
				                                                   ? "outlined"
				                                                   : "plain",
				                                "error-messages" : this.errors[ fieldName ],
				                                label :            this.$t( `model_data.${ fieldName }` ),
				                                required :         true,
				                                type :             shouldTrack
				                                                   ? type
				                                                   : "text",
			                                };
		                                },
	                                },
	                                created() {
		                                if ( this.user.card_applicant.shouldBeTracked( "first_year" ) ) {
			                                this.$watch( "first_year", ( newValue ) => {
				                                this.user.card_applicant.first_year = newValue;
			                                } );
		                                }
	                                },
                                } );
</script>

<style scoped>
</style>

<template>
    <div>
        <v-row>
            <!-- User Info Fields -->
            <v-col class = "pt-0 pb-0" cols = "12" md = "8">
                <v-text-field
                    v-model = "user.name"
                    v-bind = "getFieldProps('name', user)"
                />
            </v-col>

            <v-col class = "pt-0 pb-0" cols = "12" md = "4">
                <v-text-field
                    v-model = "user.father_name"
                    v-bind = "getFieldProps('father_name', user)"
                />
            </v-col>

            <v-col class = "pt-0 pb-0" cols = "12" md = "8">
                <v-text-field
                    v-model = "user.email"
                    v-bind = "getFieldProps('email', user, 'text')"
                />
            </v-col>

            <v-col class = "pt-0 pb-0" cols = "12" md = "4">
                <v-text-field
                    :model-value = "$t('status.'+user.status.key)"
                    v-bind = "getFieldProps('status', user)"
                />
            </v-col>

            <v-col v-if = "user.card_applicant" class = "pt-0 pb-0" cols = "12">
                <v-text-field
                    v-model = "user.card_applicant.department"
                    v-bind = "getFieldProps('department', user.card_applicant)"
                />
            </v-col>
            <template v-if = "isAcademic">
                <v-col class = "pt-0 pb-0" cols = "12" md = "8">
                    <v-text-field
                        v-model = "user.academic_id"
                        v-bind = "getFieldProps('academic_id', user)"
                    />
                </v-col>
                <v-col class = "pt-0 pb-0" cols = "12" md = "4">
                    <v-text-field
                        v-model = "user.a_m"
                        v-bind = "getFieldProps('a_m', user)"
                    />
                </v-col>

                <v-col class = "pt-0 pb-0" cols = "12" md = "8">
                    <v-text-field
                        :model-value = "$t( 'active', user.is_active ? 1: 0 ) "
                        v-bind = "getFieldProps('is_active', user, 'number')"
                    />
                </v-col>

                <v-col v-if = "user.card_applicant" class = "pt-0 pb-0" cols = "12" md = "4">
                    <v-text-field
                        :model-value = "user.card_applicant.first_year"
                        v-bind = "getFieldProps('first_year', user.card_applicant, {type :'number'})"
                    />
                </v-col>
            </template>
        </v-row>

        <!-- Address Fields -->
        <template v-if = "user.card_applicant">
        <v-row v-for = "type in ['permanent', 'temporary']" :key = "type">
            <v-col class = "pt-0 pb-0" cols = "12" md = "8">
                <v-text-field
                    v-model = "addresses[type].location"
                    v-bind = "getFieldProps('location', addresses[type],{
						required: type === 'temporary',
						label : $t('address.' + type)
					})"
                    :error-messages = "errors['addresses.'+type+'.location'] "
                />
            </v-col>

            <v-col class = "pt-0 pb-0" cols = "12" md = "4">
                <v-text-field
                    v-model = "addresses[type].phone"
                    v-bind = "getFieldProps('phone', addresses[type],{
						required: type === 'temporary',
						label : $t('address.phone.' + type)
					})"
                    :error-messages = "errors['addresses.'+type+'.phone']"
                />
            </v-col>
        </v-row>
        </template>
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
			                                rules : {
				                                require : [
					                                value => !!value || this.$t( "validation.required" ),
				                                ],
			                                },
		                                };
	                                },
	                                computed : {
		                                cardApplicant() {
			                                return cardApplicant;
		                                },
		                                addresses() : Record<string, Address> {
			                                return ( ( this.user.card_applicant?.addresses ??
			                                           [] ) as Address[] ).reduce(
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

	                                },
	                                methods :  {
		                                getFieldProps( fieldName : string, model : any,
		                                               {
			                                               type = "text",
			                                               required = true,
			                                               label = null,
		                                               } : {
			                                               type? : string;
			                                               required? : boolean;
			                                               label? : string | null
		                                               } = {}, // Default empty object
		                                ) {
			                                const shouldTrack = this.canUpdate && model.shouldBeTracked( fieldName );
			                                label ??= this.$t( `model_data.${ fieldName }` );
			                                label = this.canUpdate && required
			                                        ? `${ label } *`
			                                        : label;
			                                return {
				                                readonly :         !shouldTrack,
				                                variant :          shouldTrack
				                                                   ? "outlined"
				                                                   : "plain",
				                                "error-messages" : this.errors[ fieldName ],
				                                label :    label,
				                                rules :    ( required )
				                                           ? this.rules[ "require" ]
				                                           : [],
				                                required : required,
				                                type :             shouldTrack
				                                                   ? type
				                                                   : "text",
			                                };
		                                },
	                                },
	                                created() {
		                                if ( this.user.card_applicant?.shouldBeTracked( "first_year" ) ) {
			                                this.$watch( "first_year", ( newValue ) => {
				                                this.user.card_applicant.first_year = newValue;
			                                } );
		                                }
	                                },
                                } );
</script>

<style scoped>
</style>

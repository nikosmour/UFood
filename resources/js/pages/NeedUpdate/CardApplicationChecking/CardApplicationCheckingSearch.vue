<template>
    <v-navigation-drawer :model-value = "true" absolute permanent>
        <v-container max-width = "15rem">
            <v-card-title>{{ $t( "card.application.search" ) }}</v-card-title>
            <v-form @submit.prevent = "getId">
                <v-text-field
                    v-model = "search.application_id"
                    :label = "$t('id')"
                    clearable
                    min = "1"
                    outlined
                    type = "number"
                />
                <v-text-field
                    v-model = "search.academic_id"
                    :label = "$t('academic_id')"
                    clearable
                    min = "1"
                    outlined
                    type = "number"
                />
                <v-text-field
                    v-model = "search.a_m"
                    :label = "$t('a_m')"
                    clearable
                    min = "1"
                    outlined
                    type = "number"
                />
                <v-text-field
                    v-model = "search.email"
                    :label = "$t('email')"
                    clearable
                    outlined
                    type = "email"
                />
                <v-card-actions class = "justify-center">
                    <v-btn color = "primary" type = "submit" variant = "elevated">{{ $t( "submit" ) }}</v-btn>
                </v-card-actions>
            </v-form>

            <v-data-table
                v-if = "applications.length"
                :headers = "[{ title: $t('id'), align: 'center', key: 'id' }]"
                :hide-default-footer = "applications.length > 0"
                :items = "applications"
                class = "mt-4"
                item-key = "id"
            >
                <template v-slot:item.id = "{ item }">
                    <v-btn
                        :to = "{ name: $route.name, query: { application: item.id } }"
                        class = "nav-link"
                    >
                        {{ item.id }}
                    </v-btn>
                </template>
            </v-data-table>
        </v-container>
    </v-navigation-drawer>
</template>

<script>
export default {
	props : {
		applications : {
			type :    Array,
			default : () => [],
		},
	},
	data() {
		return {
			search : {
				application_id : null,
				academic_id :    null,
				email :          null,
				a_m :            null,
			},
			result : {
				message : "",
				success : true,
				hide :    false,
				errors :  [ "" ],
			},
		};
	},
	methods : {
		getId() {
			if ( this.search.application_id ) {
				this.$router.replace( {
					                      name :  this.$route.name,
					                      query : { application : this.search.application_id },
				                      } );
			} else if ( this.search.academic_id ) {
				this.$emit( "getId", [
					"academic_id",
					this.search.academic_id,
				] );
			} else if ( this.search.a_m ) {
				this.$emit( "getId", [
					"a_m",
					this.search.a_m,
				] );
			} else if ( this.search.email ) {
				this.$emit( "getId", [
					"email",
					this.search.email,
				] );
			} else {
				this.$displayError( { error : "card.Application.selectSearch" } );
			}
		},
	},
};
</script>

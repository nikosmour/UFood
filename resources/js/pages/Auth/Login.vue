<template>
    <v-container max-width = "40em">
                <v-card :loading = "isLoading">
                    <v-card-title id = "card_title">
                        {{ $t( "login" ) }}
                    </v-card-title>

                    <v-card-text>
                        <v-form
                            v-model = "isValid" aria-labelledby = "card_title" class = "mt-6"
                            @submit.prevent = "submitForm"
                        >
                            <!-- Email Field -->
                            <v-row class = "mb-3 justify-space-around">
                                    <v-text-field
                                        id = "email"
                                        v-model = "formData.email"
                                        :class = "{ 'is-invalid': errors.email || errors.credentials }"
                                        :error-messages = "errors.email"
                                        :label = "$t('username')"
                                        :rules = "rules['username']"
                                        @input = "errors.email=null"
                                        autofocus
                                        outlined
                                        required
                                        type = "email"
                                        max-width = "25em"
                                    />
                            </v-row>

                            <!-- Password Field -->
                            <v-row class = "mb-3 justify-space-around">
                                    <v-text-field
                                        id = "password"
                                        v-model = "formData.password"
                                        :class = "{ 'is-invalid': errors.password || errors.credentials }"
                                        :error-messages = "errors.password || errors.credentials"
                                        :rules = "rules['password']"
                                        :label = "$t('password')"
                                        @input = "errors.password=errors.credentials=null"
                                        outlined
                                        required
                                        type = "password"
                                        max-width = "25em"
                                    />
                            </v-row>

                            <!-- Submit Button -->
                            <v-row class = "mb-0">
                                <v-col cols = "12" md = "8" offset-md = "5">
                                    <v-btn
                                        :aria-label = "$t('login')" :loading = "isLoading" color = "primary"
                                        :disabled = "!isValid" type = "submit"
                                    >
                                        {{ $t( "login" ) }}
                                    </v-btn>
                                </v-col>
                            </v-row>
                        </v-form>
                    </v-card-text>
                </v-card>
    </v-container>
</template>

<script lang = "ts">
import { defineComponent } from "vue";

export default defineComponent( {
	data() {
		return {
			/**
			 * User form data for login.
			 * @type {Object}
			 */
			formData : {
				email :    "",
				password : "",
			},
			/**
			 * Object holding validation errors for form fields.
			 * @type {Object}
			 */
			errors : {},
			/**
			 * Boolean to control loading state during login process.
			 * @type {Boolean}
			 */
			isLoading : false,
			/**
			 * Boolean to check if the form is valid.
			 * @type {Boolean}
			 */
			isValid : null,

			rules : {
				username : [
					value => !!value || this.$t( "validation.required", {
						attribute : this.$t( "username" ),
					} ),
				],
				password : [
					value => !!value || this.$t( "validation.required", {
						attribute : this.$t( "password" ),
					} ),
				],
			},
		};
	},
	methods : {
		/**
		 * Submit the login form to authenticate the user.
		 * Handles validation errors and manages loading state
		 */
		async submitForm() {
			this.isLoading = true;
			this.errors = {}; // Clear any previous errors

			try {
				// Dispatch login action to Vuex store
				await this.$store.dispatch( "auth/loginUser", this.formData );
				console.log( "Login successful! User:", this.$store.state.auth.user );
			} catch ( error ) {
				// Capture server-side validation errors if available
				this.errors = error.response?.data?.errors || {};
				console.error( "Login error:", error );
			} finally {
				// Reset loading state
				this.isLoading = false;
			}
		},
	},
//     mounted(){
// 	    const query = { ...this.$route.query }; // Copy the current query object
// 	    if (query.skipAuthCheck) {
// 		    delete query.skipAuthCheck; // Remove the skipAuthCheck parameter
// 		    this.$router.replace({ query }); // Replace the query with the modified one
// 	    }
//     }
//    beforeRouteEnter(to, from, next) {
//        const query = { ...to.query }; // Copy the current query object
//
//        if (query.skipAuthCheck) {
//            delete query.skipAuthCheck; // Remove the skipAuthCheck parameter
//            next(vm => {
//                // Modify the route query before the component is created
//                vm.$router.replace({ query });
//            });
//        } else {
//            next(); // Continue to the route without modifications
//        }
//    }
                                } );
</script>

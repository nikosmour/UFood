<template>
    <v-container>
        <v-row justify = "center">
            <v-col cols = "12" md = "8">
                <v-card :loading = "isLoading">
                    <v-card-title>
                        <h5>{{ $t( "login" ) }}</h5>
                    </v-card-title>

                    <v-card-text>
                        <form @submit.prevent = "submitForm">
                            <!-- Email Field -->
                            <v-row class = "mb-3">
                                <v-col cols = "12" md = "6" offset-md = "3">
                                    <v-text-field
                                        id = "email"
                                        v-model = "formData.email"
                                        :class = "{ 'is-invalid': errors.email || errors.credentials }"
                                        :error-messages = "errors.email"
                                        :label = "$t('email')"
                                        aria-label = "$t('email')"
                                        autofocus
                                        outlined
                                        required
                                        type = "email"
                                    ></v-text-field>
                                </v-col>
                            </v-row>

                            <!-- Password Field -->
                            <v-row class = "mb-3">
                                <v-col cols = "12" md = "6" offset-md = "3">
                                    <v-text-field
                                        id = "password"
                                        v-model = "formData.password"
                                        :aria-label = "$t('password')"
                                        :class = "{ 'is-invalid': errors.password || errors.credentials }"
                                        :error-messages = "errors.password || errors.credentials"
                                        :label = "$t('password')"
                                        outlined
                                        required
                                        type = "password"
                                    ></v-text-field>
                                </v-col>
                            </v-row>

                            <!-- Submit Button -->
                            <v-row class = "mb-0">
                                <v-col cols = "12" md = "8" offset-md = "5">
                                    <v-btn
                                        :aria-label = "$t('login')" :loading = "isLoading" color = "primary"
                                        type = "submit"
                                    >
                                        {{ $t( "login" ) }}
                                    </v-btn>
                                </v-col>
                            </v-row>
                        </form>
                    </v-card-text>
                </v-card>
            </v-col>
        </v-row>
    </v-container>
</template>

<script>
export default {
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
};
</script>

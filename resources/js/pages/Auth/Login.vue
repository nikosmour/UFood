<template>
    <v-container>
        <v-row justify="center">
            <v-col cols="12" md="8">
                <v-card :loading="isLoading">
                    <v-card-title>
                        <h5>{{ $t("login") }}</h5>
                    </v-card-title>

                    <v-card-text>
                        <form @submit.prevent="submitForm">
                            <v-row class="mb-3">
                                <v-col cols="12" md="6" offset-md="3">
                                    <v-text-field
                                        id="email"
                                        v-model="formData.email"
                                        :class="{ 'is-invalid': errors.email || errors.credentials }"
                                        :error-messages="errors.email"
                                        :label="$t('email')"
                                        autofocus
                                        outlined
                                        required
                                        type="email"
                                    ></v-text-field>
                                </v-col>
                            </v-row>

                            <v-row class="mb-3">
                                <v-col cols="12" md="6" offset-md="3">
                                    <v-text-field
                                        id="password"
                                        v-model="formData.password"
                                        :class="{ 'is-invalid': errors.password || errors.credentials }"
                                        :error-messages="errors.password || errors.credentials"
                                        :label="$t('password') "
                                        outlined
                                        required
                                        type="password"
                                    ></v-text-field>
                                </v-col>
                            </v-row>

                            <v-row class="mb-0">
                                <v-col cols="12" md="8" offset-md="5">
                                    <v-btn :loading="isLoading" color="primary" type="submit">
                                        {{ $t("login") }}
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
            formData: {
                email: '',
                password: '',
                // remember: false
            },
            errors: {},
            isLoading: false
        };
    },
    methods: {
        async submitForm() {
            try {
                // 1. Fetch CSRF cookie (assuming you have a function to retrieve it)
                /*await axios.get('/sanctum/csrf-cookie').then(response => {
                    // Login...
                });
                const csrfToken = await this.getCookie('XSRF-TOKEN');*/


                // 4. Handle successful login
                this.isLoading = true;
                await this.$store.dispatch('auth/loginUser', this.formData)
                console.log('Login successful! login as ', this.$store.state.auth.user, this.$store.state.auth.isLoggedIn);
            } catch (errors) {
                // 6. Handle network or other error
                this.errors = error.response?.data?.errors || {};
                console.error('Login error:', errors);
            } finally {
                // Reset form after login attempt
                this.isLoading = false
                this.email = '';
                this.password = '';
            }
        },
    },
};
</script>

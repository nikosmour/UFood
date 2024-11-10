<script>
import {mapActions, mapGetters} from "vuex";
import NavView from "./navView.vue";

export default {
    components: {NavView},

    computed: {
        ...mapGetters('auth', [
            /**
             * Determines if the user is currently authenticated.
             * @returns {Boolean} - True if authenticated, false otherwise.
             */
            'isAuthenticated',
        ]),

        /**
         * Path to the logo image used in the footer.
         * @returns {String} - The relative path to the logo image.
         */
        imageUrl() {
            return '/img/big_logo_Upatras.png';
        },
    },

    methods: {
        ...mapActions('auth', [
            /**
             * Fetches the current authenticated user data.
             */
            'getUser',
        ]),

        /**
         * Redirects users based on authentication status and route requirements.
         * @param {Boolean} isAuthenticated - Current authentication state.
         */
        redirectAuth(isAuthenticated) {
            console.log('App.vue/redirectAuth', this.$route.fullPath, isAuthenticated, this.$route.meta.requiresAuth);
            if (!isAuthenticated && (this.$route.meta.requiresAuth || this.$route.meta.requiresAbility)) {
                this.$router.push({
                    name: 'login',
                    query: {redirect: this.$route.fullPath},
                });
            } else if (isAuthenticated && this.$route.name === 'login') {
                this.$router.push(this.$route.query.redirect || {name: 'userProfile'});
            }
        },
    },

    watch: {
        /**
         * Watches the `isAuthenticated` state and redirects based on changes.
         * @param {Boolean} newValue - New value for `isAuthenticated`.
         */
        isAuthenticated(newValue) {
            console.log('App.vue/watch/isAuthenticated', this.$route.fullPath);
            this.redirectAuth(newValue);
        },
    },

    mounted() {
        console.log('App.vue/mounted');

        // Redirect to the user profile if logged in and on login page
        if (this.isAuthenticated && this.$route.name === 'login') {
            this.$router.push(this.$route.query.redirect || {name: 'userProfile'});
        }

        // Session timeout setup with environment variable
        let timeoutMin = import.meta.env.VITE_SESSION_TIME_OUT;
        let timeout = (timeoutMin - 1) * 60000;
        if (timeoutMin < 2) timeout = 60000;

        // CSRF token refresh interval
        setInterval(() => {
            this.$axios.get(this.route('sanctum.csrf-cookie'));
        }, timeout);

        // User data refresh interval if authenticated
        setInterval(() => {
            console.log('setInterval', this.isAuthenticated);
            if (this.isAuthenticated) this.getUser();
        }, timeout);

        // Axios response interceptor for handling errors
        this.$axios.interceptors.response.use(
            response => response,
            error => {
                if (error.response.status === 419) {
                    // Handle CSRF token expiration
                    return this.$axios.get(this.route('sanctum.csrf-cookie'))
                        .then(() => this.$axios(error.config))
                        .catch(refreshError => {
                            console.error('Failed to refresh CSRF token:', refreshError);
                            return Promise.reject(error);
                        });
                } else if (error.response.status === 401) {
                    // Handle unauthorized error
                    this.$store.commit('auth/setLogout');
                    return Promise.reject(error);
                } else if (error.response.status === 403) {
                    // Redirect to 403 error page
                    this.$router.push({name: 'error.403'});
                }
                return Promise.reject(error);
            }
        );
    },
};
</script>

<template>
    <v-app>
        <!-- Main navigation component -->
        <nav-view role="navigation"/>

        <v-main>
            <!-- Main content area with role for accessibility -->
            <v-container fluid role="main">
                <router-view/>
            </v-container>
        </v-main>

        <v-footer :app="false" color="primary">
            <v-row justify="center">
                <v-col class="text-center" cols="12">
                    <div>© {{ (new Date()).getFullYear() + ' - ' + $t('company.name') }}</div>
                    <div>{{ $t('company.department') }}</div>
                    <div>{{ $t('developedBy') }}</div>

                    <!-- Logo image with translated alt text for accessibility -->
                    <v-img
                        :alt="$t('logo') + ' ' + $t('company.name')"
                        :src="imageUrl"
                        class="mx-auto"
                        max-width="200"
                    />
                </v-col>
            </v-row>
        </v-footer>
    </v-app>
</template>
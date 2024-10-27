<script>
import {mapActions, mapGetters} from "vuex";
import NavView from "./navView.vue";

export default {
    components: {NavView},
    computed: {
        ...mapGetters('auth', [
            'isAuthenticated',
        ]),
        imageUrl() {
            return '/img/big_logo_Upatras.png';
        }

    },
    methods: {
        ...mapActions('auth', [
            'getUser',
        ]),
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
        isAuthenticated(newValue) {
            console.log('App.vue/watch/isAuthenticated', this.$route.fullPath);
            this.redirectAuth(newValue);
        },
    },
    created() {
        // Uncomment if needed for debugging
        // console.log('created', this.$route, this.$router.resolve(this.$route.fullPath).fullPath, Date());
    },
    mounted() {
        this.redirectAuth(this.isAuthenticated);
        // Uncomment if needed for debugging
        // console.log('mounted', this.$route, this.$router.resolve(this.$route.fullPath).fullPath, Date());

        let timeoutMin = import.meta.env.VITE_SESSION_TIME_OUT;
        let timeout = (timeoutMin - 1) * 60000;

        if (timeoutMin < 2) timeout = 60000;

        setInterval(() => {
            this.$axios.get(this.route('sanctum.csrf-cookie'));
        }, timeout);

        setInterval(() => {
            console.log('setInterval', this.isAuthenticated);
            if (this.isAuthenticated) this.getUser();
        }, timeout);

        this.$axios.interceptors.response.use(
            response => response,
            error => {
                if (error.response.status === 419) {
                    return this.$axios.get(this.route('sanctum.csrf-cookie'))
                        .then(() => this.$axios(error.config))
                        .catch(refreshError => {
                            console.error('Failed to refresh CSRF token:', refreshError);
                            return Promise.reject(error);
                        });
                } else if (error.response.status === 401) {
                    this.$store.commit('auth/setLogout');
                    return Promise.reject(error);
                } else if (error.response.status === 403) {
                    this.$router.push({name: 'error.403'});
                }
                return Promise.reject(error);
            }
        );
    },
};
</script>

<style scoped>
/* Add any additional styles here */
</style>
<template>
    <v-app>
        <nav-view/>
        <v-main>
            <v-container fluid role="main">
                <router-view/>
            </v-container>
        </v-main>
        <v-footer :app='false' color="primary">
            <v-row justify="center">
                <v-col class="text-center" cols="12">
                    <div>© {{ (new Date()).getFullYear() + ' - ' + $t('company.name') }}</div>
                    <div>{{ $t('company.department') }}</div>
                    <div>{{ $t('developedBy') }}</div>
                    <v-img :src="imageUrl" alt="{{$t('logo') + ' ' + $t('company.name')}}" class="mx-auto"
                           max-width="200"/>
                </v-col>
            </v-row>
        </v-footer>
    </v-app>
</template>

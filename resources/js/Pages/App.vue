<script>
import {mapActions, mapGetters} from "vuex";

export default {
    computed: {
        ...mapGetters([
            'isAuthenticated',
        ]),
    },
    methods: {
        ...mapActions([
            'getUser'
        ]),
        async getCSRFToken() {
            const response = await axios.get(route('csrfToken')); // Adjust route path as needed
            return response.data.csrf_token;
        },
    },
    watch: {
        isAuthenticated(newValue) {
            if (!newValue && this.$route.meta.requiresAuth)
                this.$router.push({
                    name: 'login',
                    query: {redirect: this.$route.fullPath},
                })
            else if (newValue && this.$route.name === 'login')
                this.$router.push(this.$route.query.redirect || {name: 'userProfile'});
        }
    },
    mounted() {
        // setInterval(this.updateAxiosCSRF, 60000);
        setInterval(() => {
            if (this.isAuthenticated)
                this.getUser();
        }, 300000)
        window.axios.interceptors.response.use(
            response => response,
            error => {
                // csrf expired
                if (error.response.status === 419) {
                    // Attempt to refresh token
                    return this.getCSRFToken()
                        .then(newToken => {
                            // Update Axios headers with the new token
                            axios.defaults.headers.common['X-CSRF-TOKEN'] = newToken;
                            // Retry the original request with the refreshed token
                            return axios(error.config);
                        })
                        .catch(refreshError => {
                            // Handle refresh error (e.g., failed to refresh token)
                            console.error('Failed to refresh CSRF token:', refreshError);
                            // Optionally display an error message to the user
                            return Promise.reject(error);
                        });
                }
                return Promise.reject(error);
            }
        );
    },

}
</script>

<template>
    <div>
        <nav-view/>
        <router-view/>
    </div>
</template>

<style scoped>

</style>

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

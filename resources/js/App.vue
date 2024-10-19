<template>
    <div>
        <router-view></router-view> <!-- This is where routed components will render -->
    </div>
</template>

<script>
export default {
    name: 'App',
    methods: {
        logout() {
            // Implement your logout logic, e.g., clearing user data from the store
            this.$store.dispatch('authenticate', null); // Example of clearing user
            // Redirect to login or home
            this.$router.push('/'); // Redirect to home or login page
        }
    }
}
</script>

<style>
/* Add your global styles here */
</style>

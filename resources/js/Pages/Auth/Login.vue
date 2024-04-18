<template>
    <div>
        <h1>Login</h1>
        <form @submit.prevent="loginUser">
            <div class="form-group">
                <label for="email">Email:</label>
                <input id="email" v-model="email" required type="email">
            </div>
            <div class="form-group">
                <label for="password">Password:</label>
                <input id="password" v-model="password" required type="password">
            </div>
            <div class="form-group">
                <button type="submit">Login</button>
            </div>
            <div v-if="error">{{ error }}</div>
        </form>
    </div>
</template>

<script>

import {mapGetters} from "vuex";

export default {
    data() {
        return {
            email: '',
            password: '',
            error: null,
        };
    },
    methods: {
        async getCookie(cname) {
            let name = cname + '='
            let decodedCookie = decodeURIComponent(document.cookie)
            let ca = decodedCookie.split(';')
            for (let i = 0; i < ca.length; i++) {
                let c = ca[i];
                while (c.charAt(0) == ' ') {
                    c = c.substring(1);
                }
                if (c.indexOf(name) == 0) {
                    return c.substring(name.length, c.length);
                }
            }
            return null;
        },
        async loginUser() {
            try {
                // 1. Fetch CSRF cookie (assuming you have a function to retrieve it)
                /*await axios.get('/sanctum/csrf-cookie').then(response => {
                    // Login...
                });
                const csrfToken = await this.getCookie('XSRF-TOKEN');*/

                // 2. Prepare login data
                const loginData = {
                    email: this.email,
                    password: this.password
                };


                // 4. Handle successful login
                if (await this.$store.dispatch('loginUser', loginData)) {
                    console.log('Login successful! login as ', this.$store.state.auth.user, this.$store.state.auth.isLoggedIn);

                    // Store authentication state (e.g., tokens) or redirect
                } else {
                    // 5. Handle login errors
                    this.error = response.data.message; // Assuming response has an 'error' message
                }
            } catch (error) {
                // 6. Handle network or other errors
                console.error('Login error:', error);
                this.error = 'An error occurred. Please try again.'; // Generic error for user
            } finally {
                // Reset form after login attempt
                this.email = '';
                this.password = '';
            }
        },
        redirectAfterLogin() {
            this.$router.push(this.$route.query.redirect || {name: 'userProfile'});
        }

    },
    computed: {
        ...mapGetters([
            'isAuthenticated',
        ]),
    },
    watch: {
        isAuthenticated(newValue) {
            if (newValue)
                this.redirectAfterLogin();
        }
    },
    created() {
        if (this.isAuthenticated)
            this.redirectAfterLogin();
    }
};
</script>

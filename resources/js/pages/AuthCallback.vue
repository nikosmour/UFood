<template>
    <v-container class="d-flex align-center justify-center fill-height">
        <v-row>
            <v-col cols="12" sm="8" md="6">
                <v-card class="elevation-12">
                    <v-card-title class="text-h4 text-center">Authenticating...</v-card-title>
                    <v-card-text class="text-center">
                        <v-progress-circular
                            indeterminate
                            color="primary"
                            size="64"
                        ></v-progress-circular>
                        <p class="mt-3">Please wait while we log you in...</p>
                    </v-card-text>
                </v-card>
            </v-col>
        </v-row>
    </v-container>
</template>

<script>
export default {
    created() {
        this.getSamlResponse();
    },
    methods: {
        async getSamlResponse() {
            try {
                const response = await axios.post('/api/saml2/acs');
                const user = response.data;
                this.$store.dispatch('authenticate', user);
                this.$router.push('/');
            } catch (error) {
                console.error('Error during SAML authentication:', error);
            }
        }
    }
};
</script>

<style scoped>
.v-container {
    background-color: #f5f5f5;
}

.v-card {
    margin-top: 100px;
    padding: 20px;
}
</style>

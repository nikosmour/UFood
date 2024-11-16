<template>
    <v-snackbar v-model = "snackbar" :timeout = "4000" class = "white--text" color = "red">
        {{ message }}
    </v-snackbar>
</template>

<script>
export default {
	name : "GlobalError",
	data() {
		return {
			snackbar : false,
			message :  "",
		};
	},
	mounted() {
		// Listen for global error events
		// this.$root.$on("global-error", this.showError);
	},
	methods : {
		showError( message ) {
			this.message = message;
			this.snackbar = true; // Show snackbar
		},
	},
};
</script>

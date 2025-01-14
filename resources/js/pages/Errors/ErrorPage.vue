<!-- resources/js/Pages/NotFound.vue -->
<template>
    <v-empty-state
        :aria-live = "liveRegion"
        :image = "imageUrl"
        @click:action = "navigateToProfile"
    ></v-empty-state>
</template>

<script>

export default {
	name :     "ErrorPage",
	props :    {
		liveRegion : {
			type :    String,
			default : "assertive", // Default for aria-live region
		},
	},
	methods :  {
		navigateToProfile() {
			this.$router.push( { name : "startPage" } ); // Use router.push for navigation
		},
	},
	computed : {
		imageUrl() {
			return "/img/big_logo_Upatras.png";
		},

	},
};
</script>

<style scoped>
/* Add any custom styles here */
</style>

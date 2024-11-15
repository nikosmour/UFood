<script>
export default {
	name : "MyInfiniteScroll", // Component name

	/**
	 * @event load
	 * @description Emitted when the user scrolls to the bottom of the page.
	 */

	emits : [ "load" ],

	/**
	 * @typedef {Object} Props
	 * @property {boolean} loading - Controls the loading state (whether to show the loading spinner).
	 * @property {boolean} stopScroll - Determines if the scroll event listener should be active.
	 */

	/**
	 * @type {Props}
	 * @description The props of the component.
	 */
	props : {
		loading :    {
			type :     Boolean,
			required : true,
		},
		stopScroll : {
			type :    Boolean,
			default : false,
		},
	},

	methods : {
		/**
		 * @method handleScroll
		 * @description Handles the scroll event, checking if the user has reached the bottom of the page.
		 * If the bottom is reached, it emits the `load` event.
		 */
		handleScroll() {
			const bottomReached = window.innerHeight + window.scrollY >= document.body.offsetHeight - 100;
			if ( bottomReached ) this.$emit( "load" );
		},
	},

	watch : {
		/**
		 * @watch stopScroll
		 * @description Watches the `stopScroll` prop to dynamically add or remove the scroll event listener
		 * based on the parent's control over scroll events.
		 * @param {boolean} newValue - The new value of the `stopScroll` prop.
		 */
		stopScroll( newValue ) {
			if ( newValue ) {
				window.removeEventListener( "scroll", this.handleScroll ); // Stop listening to scroll
			} else {
				window.addEventListener( "scroll", this.handleScroll ); // Start listening to scroll
			}
		},
	},

	/**
	 * @lifecycle mounted
	 * @description Adds the scroll event listener when the component is mounted, if `stopScroll` is false.
	 */
	mounted() {
		if ( !this.stopScroll ) {
			window.addEventListener( "scroll", this.handleScroll );
		}
	},

	/**
	 * @lifecycle beforeDestroy
	 * @description Removes the scroll event listener when the component is destroyed to avoid memory leaks.
	 */
	beforeDestroy() {
		window.removeEventListener( "scroll", this.handleScroll );
	},
};
</script>

<template>
    <!-- Progress linear spinner that shows when `loading` is true -->
    <v-progress-linear
        v-if = "loading"
        color = "primary"
        indeterminate
    ></v-progress-linear>
</template>

<style scoped>
/* Add scoped styles here for the component */
</style>

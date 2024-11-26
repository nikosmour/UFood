<script>
import { mapActions, mapGetters } from "vuex";
import GlobalError from "@components/GlobalError.vue";
import NavView from "./navView.vue";
import { setupAxiosInterceptor, setupSessionTimeout } from "@utilities/sessionManager.js";

export default {
	errorCaptured( error, vm, info ) {
		console.info( `Error Captured by App.vue Info ${ info } Error message: ${ error.message }` );
		this.$refs.globalErrorComponent.showError( error.message );
		// Use the global error handler
		// this.globalErrorHandler(error.message);
		
		// Return false to prevent the error from propagating further
		return false;
	},
	
	
	components : {
		GlobalError,
		NavView,
	},
	
	computed : {
		...mapGetters( "auth", [
			/**
			 * Determines if the user is currently authenticated.
			 * @returns {Boolean} - True if authenticated, false otherwise.
			 */
			"isAuthenticated",
		] ),
		
		/**
		 * Path to the logo image used in the footer.
		 * @returns {String} - The relative path to the logo image.
		 */
		imageUrl() {
			return "/img/big_logo_Upatras.png";
		},
	},
	
	methods : {
		...mapActions( "auth", [
			/**
			 * Fetches the current authenticated user data.
			 */
			"getUser",
		] ),
		
		/**
		 * Redirects users based on authentication status and route requirements.
		 * Leave all the echo channels if the user logout;
		 * @param {Boolean} isAuthenticated - Current authentication state.
		 */
		redirectAuth( isAuthenticated ) {
			console.log( "App.vue/redirectAuth", this.$route.fullPath, isAuthenticated, this.$route.meta.requiresAuth );
			if ( !isAuthenticated && ( this.$route.meta.requiresAuth || this.$route.meta.requiresAbility ) ) {
				this.$echo.leaveAllChannels();
				this.$router.push( {
					                   name :  "login",
					                   query : { redirect : this.$route.fullPath },
				                   } );
			} else if ( isAuthenticated && this.$route.name === "login" ) {
				this.$router.push( this.$route.query.redirect || { name : "userProfile" } );
			}
		},
	},
	
	watch : {
		/**
		 * Watches the `isAuthenticated` state and redirects based on changes.
		 * @param {Boolean} newValue - New value for `isAuthenticated`.
		 */
		isAuthenticated( newValue ) {
			console.log( "App.vue/watch/isAuthenticated", this.$route.fullPath );
			this.redirectAuth( newValue );
		},
	},
	
	mounted() {
		console.log( "App.vue/mounted" );
		
		// Redirect logic if user is already authenticated
		if ( this.isAuthenticated && this.$route.name === "login" ) {
			this.$router.push( this.$route.query.redirect || { name : "userProfile" } );
		}
		
		// Session timeout setup
		const timeoutMin = import.meta.env.VITE_SESSION_TIME_OUT;
		setupSessionTimeout( timeoutMin, this.$axios, this );
		
		// Set up Axios interceptors
		setupAxiosInterceptor( this.$axios, this.$store, this.$router, this.route );
	},
	unmounted() {
		//Leave all the echo channels if the user leave the site;
		this.$echo.leaveAllChannels();
	},
};
</script>

<template>
    <v-app>
        <!-- Main navigation component -->
        <nav-view role = "navigation" />
        
        <v-main>
            <!-- Main content area with role for accessibility -->
            <v-container fluid role = "main">
                <router-view />
                <GlobalError ref = "globalErrorComponent" /> <!-- Add global error notification -->
            </v-container>
        </v-main>
        
        <v-footer :app = "false" color = "primary">
            <v-row justify = "center">
                <v-col class = "text-center" cols = "12">
                    <div>© {{ ( new Date() ).getFullYear() + " - " + $t( "company.name" ) }}</div>
                    <div>{{ $t( "company.department" ) }}</div>
                    <div>{{ $t( "developedBy" ) }}</div>
                    
                    <!-- Logo image with translated alt text for accessibility -->
                    <v-img
                        :alt = "$t('logo') + ' ' + $t('company.name')"
                        :src = "imageUrl"
                        class = "mx-auto"
                        max-width = "200"
                    />
                </v-col>
            </v-row>
        </v-footer>
    </v-app>
</template>
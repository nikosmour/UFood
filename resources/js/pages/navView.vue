<template>
    <v-app-bar app role = "navigation">
        <!-- Skip to Main Content Link for Accessibility -->
        <a :aria-label = "$t('skipToMain') " :title = "$t( 'skipToMain' ) " class = "skip-link" href = "#main"> {{
                $t( "skipToMain" ) }}</a>

        <!-- Navigation Drawer (Mobile Menu) -->
        <v-app-bar-nav-icon
            v-if = "isMobile"
            aria-label = "Open Navigation"
            @click.stop = "navigation_drawer = !navigation_drawer"
        />
        <!-- App Name -->
        <v-app-bar-title>{{ appName }}</v-app-bar-title>

        <v-spacer></v-spacer>

        <!-- Desktop Navigation (Only visible on large screens) -->
        <v-row v-if = "isAuthenticated && !isMobile" align = "center" class = "mr-4">
            <template v-for = "(button, index) in navButtons" :key = "index">
                <v-btn
                    v-if = "hasAbility(button.ability)"
                    :aria-label = "$t(button.label)"
                    :text = "$t(button.label)"
                    :to = "button.to"
                    link
                />
            </template>

            <!-- Card Menu -->
            <v-menu v-if = "hasAbility($enums.UserAbilityEnum.CARD_OWNERSHIP)" open-on-hover>
                <template v-slot:activator = "{ props }">
                    <v-btn color = "primary" v-bind = "props">
                        {{ $t( "card.value" ) }}
                    </v-btn>
                </template>
                <v-list>
                    <v-list-item :to = "{ name: 'card.History' }" link>
                        <v-list-item-title>{{ $t( "history" ) }}</v-list-item-title>
                    </v-list-item>
                    <v-list-item :to = "{ name: 'card.application' }" link>
                        <v-list-item-title>{{ $t( "application" ) }}</v-list-item-title>
                    </v-list-item>
                </v-list>
            </v-menu>

            <!-- Coupon Menu -->
            <v-menu v-if = "hasAbility($enums.UserAbilityEnum.COUPON_OWNERSHIP)" open-on-hover>
                <template v-slot:activator = "{ props }">
                    <v-btn color = "primary" v-bind = "props">
                        {{ $t( "coupon.value", 2 ) }}
                    </v-btn>
                </template>
                <v-list>
                    <v-list-item :to = "{ name: 'coupons.History' }" link>
                        <v-list-item-title>{{ $t( "history" ) }}</v-list-item-title>
                    </v-list-item>
                    <v-list-item :to = "{ name: 'coupons.transfer' }" link>
                        <v-list-item-title>{{ $t( "transfer.value" ) }}</v-list-item-title>
                    </v-list-item>
                </v-list>
            </v-menu>
        </v-row>

        <!-- Right Side Buttons (Always Visible) -->
        <v-row align = "center" class = "mr-4">
            <v-btn
                :aria-label = "$t('settings.value')"
                color = "primary"
                icon = "mdi-cog"
                @click.stop = "drawer = true"
            />

            <template v-if = "isAuthenticated">
                <v-btn
                    :aria-label = "$t('user.profile')"
                    :to = "{ name: 'userProfile' }"
                    color = "primary"
                    icon = "mdi-account-circle"
                    link
                />
                <v-btn
                    :aria-label = "$t('logout')"
                    color = "primary"
                    icon = "mdi-logout"
                    @click = "logout"
                    link
                />
                <CsrfCountdown />
            </template>

            <v-btn
                v-else
                :aria-label = "$t('login')"
                :to = "{ name: 'login' }"
                color = "primary"
                icon = "mdi-login"
                link
            />
        </v-row>
    </v-app-bar>

    <!-- Mobile Drawer -->
    <v-navigation-drawer v-if = "isMobile" v-model = "navigation_drawer " app temporary>
        <v-list>
            <template v-for = "(button, index) in navButtons" :key = "index">
                <v-list-item v-if = "hasAbility(button.ability)" :to = "button.to" color = "primary">{{
                        $t( button.label ) }}
                </v-list-item>
            </template>

            <v-list-item v-if = "hasAbility($enums.UserAbilityEnum.CARD_OWNERSHIP)">
                <v-list-group value = "card">
                    <template v-slot:activator = "{ props }">
                        <v-list-item :title = "$t( 'card.value' )" v-bind = "props" />
                    </template>
                    <v-list-item :to = "{ name: 'card.History' }" color = "primary">{{ $t( "history" ) }}</v-list-item>
                    <v-list-item :to = "{ name: 'card.application' }" color = "primary">{{ $t( "application" ) }}
                    </v-list-item>
                </v-list-group>
            </v-list-item>
            <v-list-item v-if = "hasAbility($enums.UserAbilityEnum.COUPON_OWNERSHIP)">
                <v-list-group>
                    <template v-slot:activator = "{ props }">
                        <v-list-item :title = "$t( 'coupon.value', 2 ) " v-bind = "props" />
                    </template>
                    <v-list-item :to = "{ name: 'coupons.History' }" color = "primary">{{ $t( "history" ) }}
                    </v-list-item>
                    <v-list-item :to = "{ name: 'coupons.transfer' }" color = "primary">{{ $t( "transfer.value" ) }}
                    </v-list-item>
                </v-list-group>
            </v-list-item>
        </v-list>
    </v-navigation-drawer>

    <!-- Settings Drawer -->
    <settings v-if = "drawer" v-model = "drawer" location = "right" temporary />
</template>


<script>
import { mapActions, mapGetters } from "vuex";
import Settings from "./navView/settings.vue";
import CsrfCountdown from "@components/CsrfCountdown.vue";

export default {
	name :       "NavView",
	components : {
		CsrfCountdown,
		Settings,
	},
	data() {
		return {
			/**
			 * Controls the visibility of the settings drawer.
			 * @type {Boolean}
			 */
			drawer : false,
			/**
			 * Controls the visibility of the navigation mobile drawer.
			 * @type {Boolean}
			 */
			navigation_drawer : false,
			/**
			 * Tracks the open state of the Card menu for accessibility.
			 * @type {Boolean}
			 */
			isCardMenuOpen : false,

			/**
			 * Tracks the open state of the Coupon menu for accessibility.
			 * @type {Boolean}
			 */
			isCouponMenuOpen : false,
		};
	},
	computed : {
		...mapGetters( "auth", [
			"isAuthenticated",
			"currentUser",
			"hasAbility",
		] ),
		isMobile() {
			return this.$vuetify.display.mdAndDown;
		},
		/**
		 * Returns the application name from translations.
		 * @returns {String} - The translated application name.
		 */
		appName() {
			return this.$t( "company.appName" );
		},

		/**
		 * Defines an array of navigation button configurations available to the user based on permissions.
		 * Each configuration object includes properties that specify label text, navigation route,
		 * and required user ability.
		 *
		 * @returns {Array<{ label: string, to: { name: string, params?: Object }, ability: string }>}
		 *    Array of navigation button objects, where:
		 *    - `label`: Translation key for the button text.
		 *    - `to`: Navigation route configuration.
		 *      - `to.name`: The named route to navigate to.
		 *      - `to.params`: (Optional) Route parameters for dynamic routes.
		 *    - `ability`: Required user permission to display the button.
		 */
		navButtons() {
			return [
				{
					label :   "purchase.value",
					to :      { name : "purchase" },
					ability : this.$enums.UserAbilityEnum.COUPON_SELL,
				},
				{
					label :   "entry_check",
					to :      { name : "entryChecking" },
					ability : this.$enums.UserAbilityEnum.ENTRY_CHECK,
				},
				{
					label :   "card.application.submitted",
					to :      {
						name :   "cardApplication.checking",
						params : { category : "submitted" },
					},
					ability : this.$enums.UserAbilityEnum.CARD_APPLICATION_CHECK,
				},
			];
		},
	},
	methods :  {
		/**
		 * Logs the user out
		 * @method
		 */
		...mapActions( "auth", [ "logout" ] ),
	},
	watch :    {
		/**
		 * Watches for route.name changes to update the document title.
		 * @param {String} newValue - The name of the current route.
		 */
		"$route.name"( newValue ) {
			document.title = newValue
			                 ? `${ newValue } | ${ this.appName }`
			                 : this.appName;
		},
	},
};
</script>
<style>
.skip-link {
    white-space: nowrap;
    margin: 1em auto;
    top: 0;
    position: fixed;
    left: 50%;
    margin-left: -72px;
    opacity: 0;
}

.skip-link:focus {
    opacity: 1;
    background-color: white;
    padding: 0.5em;
    border: 1px solid black;
}
</style>

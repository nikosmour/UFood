<template>
    <v-app-bar app role = "navigation">
        <v-app-bar-title>{{ appName }}</v-app-bar-title>

        <v-spacer></v-spacer>

        <v-row v-if = "isAuthenticated" align = "center" class = "mr-4">
            <template v-for = "(button, index) in navButtons" :key = "index">
                <v-btn
                    v-if = "hasAbility(button.ability)"
                    :aria-label = "$t(button.label)"
                    :text = "$t(button.label)"
                    :to = "button.to"
                    link
                />
            </template>

            <v-btn
                v-if = "hasAbility($enums.UserAbilityEnum.CARD_OWNERSHIP)"
                :aria-expanded = "isCardMenuOpen"
                :aria-label = "$t('card.value')"
                aria-haspopup = "true"
                color = "primary"
                @click = "isCardMenuOpen = !isCardMenuOpen"
            >
                {{ $t( "card.value" ) }}
                <v-menu activator = "parent" offset-y>
                    <v-list>
                        <v-list-item :to = "{ name: 'card.History' }" link>
                            <v-list-item-title>{{ $t( "history" ) }}</v-list-item-title>
                        </v-list-item>
                        <v-list-item :to = "{ name: 'card.application' }" link>
                            <v-list-item-title>{{ $t( "application" ) }}</v-list-item-title>
                        </v-list-item>
                    </v-list>
                </v-menu>
            </v-btn>

            <v-btn
                v-if = "hasAbility($enums.UserAbilityEnum.COUPON_OWNERSHIP)"
                :aria-expanded = "isCouponMenuOpen"
                :aria-label = "$t('coupon.value', 2)"
                aria-haspopup = "true"
                color = "primary"
                @click = "isCouponMenuOpen = !isCouponMenuOpen"
            >
                {{ $t( "coupon.value", 2 ) }}
                <v-menu activator = "parent" offset-y>
                    <v-list>
                        <v-list-item :to = "{ name: 'coupons.History' }" link>
                            <v-list-item-title>{{ $t( "history" ) }}</v-list-item-title>
                        </v-list-item>
                        <v-list-item :to = "{ name: 'coupons.transfer' }" link>
                            <v-list-item-title>{{ $t( "transfer.value" ) }}</v-list-item-title>
                        </v-list-item>
                    </v-list>
                </v-menu>
            </v-btn>
        </v-row>

        <v-row align = "center" class = "mr-4">
            <v-btn
                :aria-label = "$t('settings')"
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
                    link
                    @click = "logout"
                />
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

    <settings v-model = "drawer" right temporary />
</template>

<script>
import { mapActions, mapGetters } from "vuex";
import Settings from "./navView/settings.vue";

export default {
	name :       "NavView",
	components : { Settings },
	data() {
		return {
			/**
			 * Controls the visibility of the settings drawer.
			 * @type {Boolean}
			 */
			drawer : false,

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

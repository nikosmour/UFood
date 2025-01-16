import type { RouteLocationNormalized } from "vue-router";
import { createRouter, createWebHistory, type RouteRecordRaw } from "vue-router";
import store from "@/store"; // Assuming you're using Vuex for user state
import authGuard from "@/guards/AuthGuard";
import { Enums } from "@/plugins/enums";

import Unauthorized from "@pages/Errors/403.vue";
import NotFound from "@pages/Errors/404.vue";
import Login from "@pages/Auth/Login.vue";
import UserProfile from "@pages/NeedUpdate/UserProfile.vue";

const PurchaseCoupon = () => import("@pages/PurchaseCoupon.vue");
const EntryChecking = () => import("@pages/EntryChecking.vue");

const CardApplicationChecking = () => import("@pages/NeedUpdate/CardApplicationChecking.vue");
const CardApplication = () => import("@pages/Card/CardApplication.vue");
const CardTransactions = () => import("@pages/Card/CardTransactions.vue");
const TransferCoupons = () => import("@pages/Coupons/TransferCoupon.vue");
const CouponOwner = () => import("@pages/Coupons.vue");
const CouponTransactions = () => import("@pages/Coupons/CouponsTransactions.vue");
const CardApplicationCheckingSearch = () => import("@pages/NeedUpdate/CardApplicationChecking/CardApplicationCheckingSearch.vue");
const AcademicStartPage = () => import("@pages/AcademicStartPage.vue");

const routes : ReadonlyArray<RouteRecordRaw> = [
	{
		path :      "/purchase",
		name :      "purchase",
		component : PurchaseCoupon,
		meta :      { requiresAbility : Enums.UserAbilityEnum.COUPON_SELL },
	},
	{
		path :      "/entry",
		name :      "entryChecking",
		component : EntryChecking,
		meta :      { requiresAbility : Enums.UserAbilityEnum.ENTRY_CHECK },
	},
	{
		path :      "/login",
		name :      "login",
		component : Login,
	},
	{
		path :      "/myInfo",
		name :      "userProfile",
		component : UserProfile,
		meta :      { requiresAuth : true },
	},
	{
		path :      "/checking",
		component : CardApplicationChecking,
		name :      "cardApplicationChecking",
		meta :      { requiresAbility : Enums.UserAbilityEnum.CARD_APPLICATION_CHECK },
		redirect :  {
			name :   "cardApplication.checking",
			params : { category : "submitted" },
		},
		children :  [
			{
				path :      "search",
				name :      "cardApplication.checking.search",
				component : CardApplicationCheckingSearch,
			},
			{
				path :        ":category([A-z]+)",
				name :        "cardApplication.checking",
				beforeEnter : ( to, from, next ) => {
					if ( !Object.keys( Enums.CardStatusEnum )
					            .includes( to.params.category.toUpperCase() ) ) {
						// Handle invalid category (e.g., redirect to error page)
						next( { component : NotFound } );
					} else
						next();
					
				},
			} as RouteRecordRaw,
		],
	},
	{
		path :      "/error/403",
		name :      "error.403",
		component : Unauthorized,
	},
	{
		path :     "/card",
		meta :     { requiresAbility : Enums.UserAbilityEnum.CARD_OWNERSHIP },
		children : [
			{
				path :      "transactions",
				name :      "card.History",
				component : CardTransactions,
			},
			{
				path :      "application",
				name :      "card.application",
				component : CardApplication,
			},
		],
		
		
	},
	{
		path :      "/coupons",
		meta :      { requiresAbility : Enums.UserAbilityEnum.COUPON_OWNERSHIP },
		component : CouponOwner,
		children :  [
			{
				path :      "transactions",
				name :      "coupons.History",
				component : CouponTransactions,
			},
			{
				path :      "transfer",
				name :      "coupons.transfer",
				component : TransferCoupons,
			},
		],
	},
	{
		path :        "/",
		name :        "startPage",
		component :   AcademicStartPage,
		beforeEnter : ( to : RouteLocationNormalized, from : RouteLocationNormalized ) => {
			const hasAbility = store.getters[ "auth/hasAbility" ]; // Fetch user from Vuex store
			if ( hasAbility( Enums.UserAbilityEnum.ENTRY_CHECK ) )
				return {
					name : "entryChecking",
				};
			if ( hasAbility( Enums.UserAbilityEnum.COUPON_SELL ) )
				return {
					name : "purchase",
				};
			if ( hasAbility( Enums.UserAbilityEnum.COUPON_OWNERSHIP ) )
				return;
			return {
				name : "login",
			};
		},
	},
	
	{
		path :      "/:pathMatch(.*)*", // Catch-all for unmatched routes (optional, see note below)
		component : NotFound,
	},
];
const router = createRouter( {
	                             history : createWebHistory(),
	                             routes :  routes,
                             } );
router.beforeEach( authGuard );

export default router;

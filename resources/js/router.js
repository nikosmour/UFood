import {createWebHashHistory, createRouter} from 'vue-router'
import authGuard from "./guards/AuthGuard";

const Unauthorized = () => import('./Pages/Errors/403.vue');
const PurchaseCoupon = () => import('./Pages/PurchaseCoupon.vue');
const EntryChecking = () => import('./Pages/EntryChecking.vue');
const Login = () => import('./Pages/Auth/Login.vue');
const UserProfile = () => import("./Pages/UserProfile.vue");
const CardApplicationChecking = () => import("./Pages/CardApplicationChecking.vue");
const CardApplication = () => import("./Pages/CardApplicationEditForm.vue");
const CardApplicationCreate = () => import("./Pages/CardApplicationCreateForm.vue");
const Transactions = () => import("./Pages/Transactions.vue");

const routes = [
    {
        path: '/purchase', name: 'purchase', component: PurchaseCoupon,
        meta: {requiresAuth: true},
    },
    {
        path: '/entry', name: 'entryChecking', component: EntryChecking,
        meta: {requiresAuth: true},
    },
    {
        path: '/login', name: 'login', component: Login
    },
    {
        path: '/myInfo', name: 'userProfile', component: UserProfile,
        meta: {requiresAuth: true},
    },
    {
        path: '/checking/:category', name: 'cardApplication.Checking', component: CardApplicationChecking,
        meta: {requiresAuth: true},
        children: [
            {path: 'application/:application', name: 'cardApplicationChecking.application'},
        ]
    },
    {
        path: '/error/403', name: 'error.403', component: Unauthorized,
    },
    {
        path: '/card',
        meta: {requiresAuth: true},
        children: [
            {
                path: 'transactions',
                name: 'card.History',
                component: Transactions,
                props: {
                    urlName: 'card.history',
                }
            },
            {
                path: 'application',
                name: 'card.application',
                component: CardApplication,
            },
            {
                path: 'application/create',
                name: 'card.application.create',
                component: CardApplicationCreate,
            }
        ]


    },
    {
        path: '/coupons',
        meta: {requiresAuth: true},
        component: CouponOwner,
        children: [
            {
                path: 'transactions',
                name: 'coupons.History',
                component: Transactions,
                props: {
                    urlName: 'coupons.history',
                }
            },
        ]
    }
];
const router = createRouter({
    history: createWebHashHistory(),
    routes,
});
router.beforeEach(authGuard);

export default router;

import {createWebHashHistory, createRouter} from 'vue-router'
import authGuard from "./guards/AuthGuard";
import enums from "./enums";

const PurchaseCoupon = () => import('./Pages/PurchaseCoupon.vue');
const EntryChecking = () => import('./Pages/EntryChecking.vue');
const Login = () => import('./Pages/Auth/Login.vue');
const UserProfile = () => import("./Pages/UserProfile.vue");
const CardApplicationChecking = () => import("./Pages/CardApplicationChecking.vue");
const CardApplicationShowData = () => import("./Components/CardApplicationShowData.vue")

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
    }
];
const router = createRouter({
    history: createWebHashHistory(),
    routes,
});
router.beforeEach(authGuard);

export default router;

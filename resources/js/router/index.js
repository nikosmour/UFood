import {createRouter, createWebHashHistory, createWebHistory} from 'vue-router';
import Home from '../pages/Home.vue';
const routes = [
    {
        path: '/',
        name: 'Home',
        component: Home
    },
    {
        path: '/auth/callback',
        name: 'AuthCallback',
        component: () => import('../pages/AuthCallback.vue')
    }
];

const router = createRouter({
    history: createWebHashHistory(),
    routes,
});

export default router;

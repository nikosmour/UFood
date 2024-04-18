// authGuard.js
import {nextTick} from 'vue'; // Import for Vue 3 compatibility
import store from '../store/auth.js'

export default async (to, from) => {
    // Check for routes requiring authentication
    console.log(Date.now() % 10000, 'AuthGuard');
    if (to.meta.requiresAuth) {
        if (store.getters['waitingForUser']) await store.getters['waitingForUser'];
        if (!store.getters['isAuthenticated'])
            return {
                name: 'login',
                query: {redirect: to.fullPath},
            };
    }
};

// authGuard.js
import {nextTick} from 'vue'; // Import for Vue 3 compatibility
import store from '../store/auth.js'

export default async (to, from) => {
    // Check for routes requiring authentication
    console.log(Date.now() % 10000, 'AuthGuard');
    if ((to.meta.requiresAuth || to.meta.requiresAbility) && !store.getters['isAuthenticated']) {
            return {
                name: 'login',
                query: {redirect: to.fullPath},
            };
    }
    if (to.meta.requiresAbility && !store.getters['hasAbility'](to.meta.requiresAbility)) {
        return {
            name: 'error.403',
        };
    }
};

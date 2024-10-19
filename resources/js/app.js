import { createApp } from 'vue';            // Vue 3's createApp
import App from './App.vue';                // Your App.vue component
import router from './router/index.js';              // Your router file
import store from './store';                // Import the store from the separate file
import vuetify from './plugins/vuetify';    // Import Vuetify from the separate file
import axiosInstance from './plugins/axios'; // Import Axios instance

// Create Vue application instance
const app = createApp(App);

// Make Axios globally available
app.config.globalProperties.$axios = axiosInstance;

// Use Vue Router, Vuex, and Vuetify plugins with the app
app.use(router);
app.use(store);
app.use(vuetify);

// Mount the app to the DOM
app.mount('#app');

import { defineConfig } from 'vite';
import laravel from 'laravel-vite-plugin';
import vue from '@vitejs/plugin-vue'; // Import the Vue plugin
import vuetify from 'vite-plugin-vuetify';


export default defineConfig({
    plugins: [
        laravel({
            input: ['resources/js/app.js', 'resources/css/app.css'],
        }),
        vue(), // Use the Vue plugin
        vuetify({
            autoImport: true,  // Enable auto-import for Vuetify components
        }),
    ],
    server: {
        host: '127.0.6.0', // You can set this to your desired host
        port: 3000, // You can change this port if needed
        hmr: {
            host: '127.0.6.0', // Change to your domain if required
        },
    },
});

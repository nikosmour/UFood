import {defineConfig} from 'vite';
import laravel from 'laravel-vite-plugin';
import vue from '@vitejs/plugin-vue'; // Import the Vue plugin
import vuetify from 'vite-plugin-vuetify';


export default defineConfig(({mode}) => {
    return {
    plugins: [
        laravel({
            input: ['resources/js/main.js', 'resources/sass/app.scss'],
        }),
        vue(), // Use the Vue plugin
        vuetify({
            autoImport: true,  // Enable auto-import for Vuetify components
        }),
    ],
        server: mode === 'development' ? {
            host: '127.0.3.0',  // Set this to the correct dev host
            port: 3000, // Port for dev server
        hmr: {
            host: process.env.VITE_HMR_HOST,  // Set to your HMR host if needed
        },
        } : undefined,
        build: {
            minify: 'terser',  // Use Terser for minification
            terserOptions: {
                compress: {
                    // Remove all console statements (console.log, console.info, etc.)
                    drop_console: true,

                    // Remove all debugger statements
                    drop_debugger: true,
                },
                output: {
                    // Remove all comments from the final output
                    comments: false,
                },
        },
    },
    }
});

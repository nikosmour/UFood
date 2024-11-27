import {defineConfig} from "vite";
import laravel from "laravel-vite-plugin";
import vue from "@vitejs/plugin-vue"; // Import the Vue plugin
import vuetify from "vite-plugin-vuetify";
import path from "path";

const path2 = path.resolve( __dirname, "resources", "js" );
export default defineConfig( ( { mode } ) => {
	return {
		plugins : [
			laravel( {
				         input : [
					         "resources/js/main.js",
					         "resources/sass/app.scss",
				         ],
			         } ),
			vue(), // Use the Vue plugin
			vuetify( {
				         autoImport : true,  // Enable auto-import for Vuetify components
			         } ),
		],
		resolve : {
			alias : {
				"@" :           path2,
				"@components" : path.resolve( path2, "components" ),
				"@services" :   path.resolve( path2, "services" ),
				"@store" :      path.resolve( path2, "store" ),
				"@utilities" :  path.resolve( path2, "utilities" ),
				"@pages" :      path.resolve( path2, "pages" ),
				"@models" :     path.resolve( path2, "models" ),
				"@enums" :      path.resolve( path2, "enums" ),
                "@types": path.resolve(path2, "types"),
				"ziggy-js" :    path.resolve( __dirname, "vendor", "tightenco", "ziggy" ),
			},
		},
		server :  mode === "development"
		          ? {
				host : "127.0.3.0",  // Set this to the correct dev host
				port : 3000, // Port for dev server
				hmr :  {
					host : process.env.VITE_HMR_HOST,  // Set to your HMR host if needed
				},
			}
		          : undefined,
		build :   {
			minify :        "terser",  // Use Terser for minification
			terserOptions : {
				compress : {
					// Remove all console statements (console.log, console.info, etc.)
					drop_console : true,
					
					// Remove all debugger statements
					drop_debugger : true,
				},
				format: {
					// Remove all comments from the final output
					comments : false,
				},
			},
		},
	};
} );

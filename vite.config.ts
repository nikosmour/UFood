import { defineConfig } from "vite";
import laravel from "laravel-vite-plugin";
import vue from "@vitejs/plugin-vue"; // Import the Vue plugin
import vuetify from "vite-plugin-vuetify";
import path from "path";
import compression from "vite-plugin-compression";

const path2 = path.resolve( __dirname, "resources", "js" );
export default defineConfig( ( { mode } ) => {
	return {
		plugins : [
			laravel( {
				         input : [
					         "resources/js/main.js",
				         ],
			         } ),
			vue(), // Use the Vue plugin
			vuetify( {
				         autoImport : true,  // Enable auto-import for Vuetify components
			         } ),
			compression( {
				             algorithm : "gzip", // Gzip compression
				             threshold : 5012,   // Files larger than 10KB will be compressed
				             ext :       ".gz",         // Gzipped files will have a `.gz` extension
			             } ),
			compression( {
				             algorithm : "brotliCompress", // Brotli compression
				             threshold : 5012,            // Files larger than 5KB will be compressed
				             ext :       ".br",                  // Brotli compressed files will have a `.br` extension
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
		esbuild : {
			target : ( mode === "development" )
			         ? "esnext"
			         : "es6", // ECMAScript for build
		},
		build :   {
			minify :        "terser",  // Use Terser for minification
			terserOptions : {
				mangle : {
					reserved : [
						"Academic",
						"CardApplicant",
						"Address",
						"CardApplicationDocument",
					],
				},
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
			rollupOptions : {
				output : {
					inlineDynamicImports : true,
					// manualChunks( id ) {
					// if ( id.includes( path.resolve( path2, "/pages/Coupons/" ) ) ||
					//      id.includes( path.resolve( path2, "/pages/Coupons.vue" ) )
					// ) {
					// 	return "coupons"; // All coupon-related files go into "coupons.js"
					// }
					// if ( id.includes( path.resolve( path2, "/pages/Card/" ) ) ) {
					// 	return "card"; // All card-related files go into "card.js"
					// }
					// // if ( id.includes( path.resolve( path2, "components/" ) ) ) {
					// // 	return "components"; //All common components on "component"
					// // }
					// // if ( id.includes( path.resolve( path2, "/pages/Errors/" ) ) ||
					// //      id.includes( path.resolve( path2, "/pages/Auth/Login.vue" ) ) ||
					// //      id.includes( path.resolve( path2, "/pages/NeedUpdate/UserProfile.vue" ) )
					// // ) {
					// // 	return 'base'; // All the basic Page go into "base.js"
					// // }
					// if ( id.includes( path.resolve( path2, "/pages/NeedUpdate/CardApplicationChecking/" ) ) ||
					//      id.includes( path.resolve( path2, "/pages/NeedUpdate/CardApplicationChecking.vue" ) )
					// ) {
					// 	return "application_check"; // All CardApplicationChecking-related files go into "application_check.js"
					// }
					// },
				},
			},
		},
	};
} );

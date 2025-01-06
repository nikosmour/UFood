import el from "./el";
import en from "./en";
import * as $vuetify from "vuetify/locale";

const langs = {};
for ( const lang in $vuetify ) {
	langs[ lang ] = { "$vuetify" : $vuetify[ lang ] };
}
langs[ "el" ] = el;
Object.assign( langs[ "en" ], en );
export default langs;
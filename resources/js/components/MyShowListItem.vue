<template>
    <v-list>
        <template v-for = "(items, listKey) in listItems" :key = "listKey">
            <v-list-item v-for = "(value, key) in items" :key = "key">
                <v-row v-if = "showAlwaysItems || value" align-content = "center">
                    <v-col class = " d-flex justify-end" cols = "6">
                        <v-list-item-title>{{ $t( listKey + "." + key.toLowerCase() ) }} :</v-list-item-title>
                    </v-col>
                    <v-col align-self = "center" cols = "6">
                        <v-list-item-subtitle>{{ value }}</v-list-item-subtitle>
                    </v-col>
                </v-row>
            </v-list-item>
            <v-divider />
        </template>
    </v-list>
</template>
<script>
export default {
	name :  "MyShowListItem",
	props : {
		listItems :       {},
		showAlwaysItems : false,
	},
	created() {
		console.log( "MyShowListItem created" );
	},
	mounted() {
		console.log( "MyShowListItem created" );
	},
};
</script>
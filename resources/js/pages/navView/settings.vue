<template>
    <v-navigation-drawer>
        <v-list id = "moreSettings">
            <v-list-item>
                <select-theme class = "mt-4" />
            </v-list-item>
            <v-list-item>
                <update-lang class = "mt-4" />
            </v-list-item>
        </v-list>
    </v-navigation-drawer>
</template>

<script>
import SelectTheme from "./selectTheme.vue";
import UpdateLang from "@pages/navView/selectLang.vue";

export default {
	name :       "Settings",
	components : {
		UpdateLang,
		SelectTheme,
	},
};
</script>

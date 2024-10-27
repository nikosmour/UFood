<template>
    <v-navigation-drawer>
        <v-list>
            <v-list-item>
                <v-list-item-action>
                    <select-theme class="mt-4"/>
                </v-list-item-action>
            </v-list-item>
        </v-list>
    </v-navigation-drawer>
</template>

<script>
import SelectTheme from "./selectTheme.vue";

export default {
    name: "Settings",
    components: {SelectTheme}
};
</script>

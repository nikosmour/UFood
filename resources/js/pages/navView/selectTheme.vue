// selectTheme.vue
<template>
    <v-select
        v-model="themeCategory"
        :items="['light', 'dark', 'system']"
        :label="$t('theme.select')"
        variant="outlined"
        v-on:update:model-value="updateTheme"
    ></v-select>
</template>

<script>
export default {
    name: "SelectTheme",
    data() {
        return {
            themeCategory: localStorage.getItem('settings.theme') || 'system'
        };
    },
    methods: {
        getPreferredTheme() {
            return window.matchMedia('(prefers-color-scheme: light)').matches ? 'light' : 'dark';
        },
        updateTheme() {
            console.log(this.themeCategory);
            localStorage.setItem('settings.theme', this.themeCategory);
            this.$vuetify.theme.global.name = this.themeCategory === 'system' ? this.getPreferredTheme() : this.themeCategory;
        }
    },
    created() {
        this.updateTheme();
    }
};
</script>

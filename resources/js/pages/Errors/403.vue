<template>
    <v-empty-state
        :action-text="$t('errors.navigateAfter')"
        :aria-live="liveRegion"
        :image="imageUrl"
        :text="$t('errors.forbiddenAccess.details')"
        :title="$t('errors.forbiddenAccess.value')"
        headline="Whoops, 403"
        @click:action="navigateToProfile"
    ></v-empty-state>
</template>

<script>
export default {
    props: {
        liveRegion: {
            type: String,
            default: "assertive", // Default for aria-live region
        },
    },
    methods: {
        navigateToProfile() {
            this.$router.push({name: 'userProfile'}); // Use router.push for navigation
        },
    },
    computed: {
        imageUrl() {
            return '/img/big_logo_Upatras.png';
        }

    },

};
</script>

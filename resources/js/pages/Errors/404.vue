<!-- resources/js/Pages/NotFound.vue -->
<template>
    <error-page
        :action-text = "$t('errors.navigateAfter')"
        :text = "$t('errors.pageNotFound.details')"
        :title = "$t('page') +' ' + $t('found',0).toLowerCase() "
        headline = "Whoops, 404"
    />
</template>

<script>
import ErrorPage from "@pages/Errors/ErrorPage.vue";

export default {
	name :       "404",
	components : { ErrorPage },
};
</script>

<style scoped>
/* Add any custom styles here */
</style>

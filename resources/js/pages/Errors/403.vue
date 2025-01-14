<template>
    <error-page
        :action-text = "$t('errors.navigateAfter')"
        :text = "$t('errors.forbiddenAccess.details')"
        :title = "$t('errors.forbiddenAccess.value')"
        headline = "Whoops, 403"
    />
</template>

<script>
import ErrorPage from "@pages/Errors/ErrorPage.vue";

export default {
	name :       "404",
	components : { ErrorPage },
};
</script>

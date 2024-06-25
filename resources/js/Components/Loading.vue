<script>
export default {
    name: "Loading.vue",

}
</script>

<template>
    <div class="text-center mb-3">
        <div class="spinner-border text-primary" role="status">
            <span class="visually-hidden">{{ $t("loading") }}...</span>
        </div>
    </div>

</template>

<style scoped>

</style>

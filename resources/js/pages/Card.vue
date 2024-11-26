<script>
import MyFreeFoodStatus from "@components/MyFreeFoodStatus.vue";

export default {
	name :       "Card",
	components : { MyFreeFoodStatus },
};
</script>

<template>
    <div>
        <my-free-food-status />
        <router-view />
    </div>
</template>
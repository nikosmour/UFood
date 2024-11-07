<script>
import {mapState} from "vuex";
import ModelsToTable from "../components/modelsToTable.vue";

export default {
    components: {ModelsToTable},
    computed: {
        ...mapState({
            couponOwner: state => state.auth.user.coupon_owner, // Assuming couponOwner data is in Vuex state
        }),
    },
}
</script>

<template>
    <div>
        <models-to-table :aria-label="$t('coupon.owner.info')" :caption="$t('coupon.owner')" :models="[couponOwner]"/>
        <router-view :couponOwner="couponOwner"/>
    </div>
</template>

<style scoped>

</style>

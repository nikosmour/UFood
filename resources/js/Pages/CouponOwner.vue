<script>
import {mapActions, mapGetters, mapState} from "vuex";
import ModelsToTable from "../Components/modelsToTable.vue";

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
        <models-to-table :models="[couponOwner]" caption="coupon_owner"/>
        <router-view :couponOwner="couponOwner"/>
    </div>
</template>

<style scoped>

</style>

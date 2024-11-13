<template>
    <div>
      <h1>{{ $t('Welcome') }}, {{ currentUser?.name }}!</h1>
        <my-models-to-table :caption="$t('user.information')" :models="currentUser? [currentUser]:[]"/>
    </div>
</template>

<script>
import {mapGetters} from 'vuex';
import MyModelsToTable from "../components/MyModelsToTable.vue";

export default {
    components: {MyModelsToTable},
    computed: {
        ...mapGetters('auth', [
            'currentUser',
        ]),
    },
};
</script>

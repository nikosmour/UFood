<template>
    <div>
      <h1>{{ $t('Welcome') }}, {{ currentUser?.name }}!</h1>
      <models-to-table :caption="$t('user.information')" :models="currentUser? [currentUser]:[]"/>
    </div>
</template>

<script>
import {mapGetters} from 'vuex';
import ModelsToTable from "../components/modelsToTable.vue";

export default {
    components: {ModelsToTable},
    computed: {
        ...mapGetters('auth', [
            'currentUser',
        ]),
    },
};
</script>

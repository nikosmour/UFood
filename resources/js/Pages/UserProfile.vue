<template>
    <div>
        Welcome, {{ currentUser.name }}!
        <models-to-table :models="[currentUser]" caption="user"/>
    </div>
</template>

<script>
import {mapGetters} from 'vuex';
import ModelsToTable from "../Components/modelsToTable.vue";

export default {
    components: {ModelsToTable},
    computed: {
        ...mapGetters([
            'currentUser',
        ]),
    },
};
</script>

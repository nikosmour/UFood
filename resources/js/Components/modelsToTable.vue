<template>
    <v-data-table
        :headers="tableHeaders"
        :items="models"
        :show-expand="relationships.length !== 0"
        hide-default-footer
        items-per-page=-1
    >
        <template v-slot:top>
            <v-toolbar flat>
                <v-toolbar-title>{{ caption }}</v-toolbar-title>
            </v-toolbar>
        </template>
        <template v-slot:expanded-row="{ columns, item }">
            <models-to-table v-for="(relationship, index) in relationships" :key="'relationship-' + index"
                             :caption="$t('model_data.'+relationship)" :models="relationshipData(item[relationship])"/>
        </template>

    </v-data-table>
</template>

<script>
// import ModelsToTableComponent from './modelsToTable.vue';
export default {
    name: 'ModelsToTable',
    // components: {ModelsToTable: ModelsToTableComponent},
    props: {
        models: {
            type: Array,
            required: true,
        },
        caption: {
            type: String,
            default: 'Table',
        },
    },
    data() {
        return {
            expandedRelationships: new Set(),
        };
    },
    computed: {
        firstModel() {
            return this.models.length > 0 ? this.models[0] : {};
        },
        attributes() {
            return Object.keys(this.firstModel).filter(key => typeof this.firstModel[key] !== 'object');
        },
        relationships() {
            return Object.keys(this.firstModel).filter(key => typeof this.firstModel[key] === 'object');
        },
        tableHeaders() {
            return this.attributes.map(key => ({
                title: this.$t("model_data." + key.toLowerCase()),
                value: key,
                key: key
            }));
        },
    },
    methods: {
        relationshipData(data) {
            return Array.isArray(data) ? data : [data];
        },
        toggleRelationship(index) {
            if (this.expandedRelationships.has(index)) {
                this.expandedRelationships.delete(index);
            } else {
                this.expandedRelationships.add(index);
            }
        },
    },
};
</script>

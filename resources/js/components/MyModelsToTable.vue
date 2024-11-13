<template>
    <v-data-table
        v-model:expanded="expanded"
        :headers="tableHeaders"
        :items="models"
        :show-expand="relationships.length !== 0"
        hide-default-footer
        items-per-page=-1
    >
        <template v-slot:top>
            <v-toolbar :title="caption" flat/>
        </template>
        <template v-slot:expanded-row="{item }">
            <tr v-for="(relationship, index) in relationships" :key="'relationship-' + index">
                <td :colspan="tableHeaders.length+1">
                    <my-models-to-table v-if="typeof relationshipData(item[relationship])[0] === 'object'"
                                     :caption="$t('model_data.'+relationship)"
                                     :models="relationshipData(item[relationship])"/>
                    <v-list v-else>
                        <v-list-subheader :title="relationship"/>
                        <v-list-item v-for="(value, index2) in relationshipData(item[relationship])"
                                     :key="relationship + '-' + index2" :title="value"/>

                    </v-list>
                </td>
            </tr>
        </template>
        <template v-slot:item.created_at="{item}">
            {{ (new Date(item.created_at)).toLocaleDateString() }}
        </template>
        <template v-slot:item.updated_at="{item}">
            {{ (new Date(item.updated_at)).toLocaleDateString() }}
        </template>

    </v-data-table>
</template>

<script>
export default {
    name: 'MyModelsToTable',
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
            expanded: [],
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
    },
};
</script>

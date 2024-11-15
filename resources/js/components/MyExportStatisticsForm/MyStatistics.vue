<template>
    <v-overlay
        class = "align-center justify-center" v-bind:model-value = "overlay"
        v-on:update:model-value = "$emit('update:overlay')"
    >
        <v-card>
            <v-card-title class = "d-flex justify-end">
                <v-btn icon size = "x-small" @click = "$emit('update:overlay')">
                    <v-icon>mdi-close</v-icon>
                </v-btn>
            </v-card-title>
            <v-card-text>
                <v-container fluid>
                    <my-models-to-table :caption = "$t('statistics.meals')" :models = "received_statistics" />
                </v-container>
            </v-card-text>

        </v-card>
    </v-overlay>
</template>

<script>
import MyModelsToTable from "../MyModelsToTable.vue";

export default {
	name :       "MyStatistics",
	components : { MyModelsToTable },
	props :      {
		received_statistics : Array,
		overlay :             Boolean,
	},
	emits :      [
		"update:overlay",
	],
};
</script>

<style scoped>
</style>

<template>
    <v-overlay class="align-center justify-center" v-bind:model-value="overlay"
               v-on:update:model-value="$emit('update:overlay')">
        <v-card max-width="400">
            <v-card-title class="d-flex justify-end">
                <v-btn icon size="x-small" @click="$emit('update:overlay')">
                    <v-icon>mdi-close</v-icon>
                </v-btn>
            </v-card-title>
            <v-card-text>
                <div v-html="html"></div>
            </v-card-text>

        </v-card>
    </v-overlay>
</template>

<script>
export default {
    props: {
        html: String,
        overlay: Boolean,
    },
    emits: [
        'update:overlay',
    ],
};
</script>

<style scoped>
.message-container {
    position: fixed;
    top: 50%;
    left: 50%;
    transform: translate(-50%, -50%);
    background-color: #fff;
    padding: 20px;
    border: 1px solid #ccc;
    box-shadow: 0 2px 4px rgba(0, 0, 0, 0.1);
    z-index: 1; /* Ensure the message is displayed on top */
}

.close-button {
    position: absolute;
    top: 5px;
    right: 5px;
    cursor: pointer;
}
</style>

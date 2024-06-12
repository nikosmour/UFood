<template>
    <div class="message-container">
        <div v-html="html"></div>
        <button class="close-button" @click="$emit('destroy')">x</button>
    </div>
</template>

<script>
export default {
    props: {
        html: String,
    }
}
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

<template>
    <div :aria-live="liveRegion" class="error-message">
        <h1 class="error-message__title">Error: Forbidden Access</h1>
        <p class="error-message__text">
            You are not authorized to access this resource. Please check your permissions or log in if you have an
            account.
        </p>
    </div>
</template>

<script>
export default {
    props: {
        liveRegion: String, // Optional prop to set aria-live region (e.g., "assertive")
    },
    computed: {
        // Provide a default for liveRegion if not explicitly set
        defaultLiveRegion() {
            return 'assertive';
        },
    },
    methods: {
        // No methods required in this basic implementation
    },
};
</script>

<style scoped>
/* WCAG 2.2 Considerations: */
.error-message {
    color: #c0392b; /* Red for error indication (WCAG 2.1 contrast ratio of 4.5) */
    background-color: #f5f5f5; /* Light background for better readability */
    padding: 1rem;
    border: 1px solid #ddd;
    border-radius: 4px;
    text-align: center; /* Center text for consistent alignment */
}

.error-message__title {
    font-weight: bold;
    margin-bottom: 0.5rem; /* Add a bit of spacing below the title */
}

.error-message__text {
    font-size: 1rem; /* Adjust font size as needed */
    line-height: 1.5; /* Improve readability with line spacing */
}
</style>

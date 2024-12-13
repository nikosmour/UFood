<template>
    <v-snackbar
        v-model = "options.showError"
        :color = "options.color"
        :timeout = "options.timeout"
        class = "white--text"
        top
    >
        {{ options.message }}
        <template #actions>
            <v-btn color = "white" variant = "text" @click = "options.showError = false">Close</v-btn>
        </template>
    </v-snackbar>
</template>

<script>
import { useNotifyHandler } from "@components/NotifyUser/NotifyPlugin";

export default {
	setup() {
		const { options } = useNotifyHandler;
		return { options };
	},
};
</script>

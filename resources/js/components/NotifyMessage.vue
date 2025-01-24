<template>
    <v-snackbar
        v-model = "options.showError"
        :color = "options.color"
        :timeout = "options.timeout"
        class = "white--text"
        close-on-content-click
        role = "alert"
        location = "top right"
    >
        {{ options.message }}
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

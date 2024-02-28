<template>
    <label v-if="show" v-bind:class="getClass">{{ message + errormessage }}</label>
</template>

<script>
export default {
    props: {
        message: String,
        success: Boolean,
        hide: {
            type: Boolean,
            default: false
        },
        errors: {default: []}
    },
    data: function () {
        return {
            show: !this.hide,
        }
    },
    computed: {
        getClass: function () {
            return {
                'text-success': this.success,
                'text-danger': !this.success
            }
        },
        errormessage: function () {
            let message = ''
            for (let error in this.errors) {
                message = message + ' ' + error + ' => ' + this.errors[error];
                console.log(error, this.errors[error])
            }
            return message;
        }
    },
    methods: {
        hideShowMessage(show, time = 2500) {
            setTimeout(() => this.show = show, time)
        },
        hideMessage() {
            this.hideShowMessage(false)
        },
        showMessage() {
            this.hideShowMessage(true, 500)
        }
    },
    watch: {
        message(newValue, oldValue) {
            if (this.hide && newValue) {
                this.showMessage();
                this.hideMessage();
            }
        }
    },
}
</script>

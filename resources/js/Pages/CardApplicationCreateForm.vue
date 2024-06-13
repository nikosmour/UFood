<template>
    <div>
        <models-to-table :caption="$t('user')" :models="[currentUser]" aria-label="User Table"/>
        <form id="accept-form" aria-label="Accept Form" method="POST" @submit.prevent="createApplication">
            <button aria-label="Accept" class="btn btn-primary">{{ $t('Accept') }}</button>
        </form>
        <message v-bind="result"/>
    </div>
</template>


<script>
import {mapGetters} from 'vuex';
import ModelsToTable from "../Components/modelsToTable.vue";
import Message from "../Components/Message.vue";

export default {
    components: {Message, ModelsToTable},
    data: function () {
        return {
            academic_id: '',
            url: route('cardApplication.store'),
            result: {
                message: this.$t('ready'),
                success: true,
                hide: true,
                errors: ['']
            },
        }
    },

    computed: {
        ...mapGetters([
            'currentUser',
        ]),
    },
    methods: {
        createApplication() {
            if (0 === this.academic_id) return;
            this.result.message = '';
            axios.post(this.url).then(responseJson => {
                let json = responseJson.data;
                this.result.success = json.success;
                if (json.success) {
                    this.result.message = this.$t('successful_transfer');
                    this.result.errors = [];
                    setTimeout(() => this.$router.push({name: 'card.application'}), 2000);
                    return;
                }
                this.result.message = this.$t('Request failed');
                this.result.errors = json;
            }).catch(errors => {
                this.result.success = false;
                this.result.errors = errors.response.data.errors;
                console.log(errors.response.data.errors);
                this.result.message = this.$t('Request failed');
            });
        }
    }
};
</script>

<style scoped>
/* Ensure sufficient color contrast */
button.btn-primary {
    background-color: #0056b3; /* Darken the primary color */
    color: #ffffff;
}

button.btn-primary:hover {
    background-color: #004494; /* Darken the hover color */
}
</style>

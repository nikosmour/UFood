<template>
    <div>
        <models-to-table :models="[currentUser]" caption="user"/>
        <form id="accept-form" method="POST" v-on:submit.prevent="createApplication">
            <button>Accept</button>
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
                message: 'ready',
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
            if (0 === this.academic_id)
                return;
            this.result.message = ''; //#todo more clever way to show if the value is the same
            axios.post(this.url
            ).then(responseJson => {
                let json = responseJson['data'];
                this.result.success = json['success'];
                if (json['success']) {
                    this.result.message = 'the application has been created';
                    this.result.errors = [];
                    setTimeout(() => this.$router.push({name: 'card.application'}), 2000)
                    return
                }
                this.result.message = "Request failed:";
                this.result.errors = json;
            }).catch(errors => {
                this.result.success = false;
                this.result.errors = errors.response.data.errors;
                console.log(errors.response.data.errors)
                this.result.message = "Request failed:";

            });
        }
    }
};
</script>

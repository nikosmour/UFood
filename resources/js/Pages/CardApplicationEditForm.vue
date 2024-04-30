<template>
    <div>
        <p>Your Application status is {{ status }} and the expiration date is {{ expiration_date }}</p>
    <div class='row '>
        <div class='col-5'>
            <header>
                <br/>
                <h4 class="text-left">{{ title }}</h4>
            </header>
            <form v-if="applicationEdit" id="card_application_form" v-on:submit.prevent="">
                <CardDocumentsShow ref="CardDocuments" v-bind:applicationEdit="applicationEdit"
                                   v-bind:cardApplication="cardApplication?.id"
                                   v-on:previewFile="this.docLink= $event"/>
                <div>
                    <label for="commentStudent">Enter Comment:</label>
                    <input id="commentStudent" v-model="commentStudent" type="text">

                </div>

                <button v-if="applicationEdit" class="btn btn-primary" type="submit" @click="submit_form">Submit
                </button>
            </form>
            <CardDocumentsShow v-else v-bind:applicationEdit="applicationEdit"
                               v-bind:cardApplication="cardApplication?.id"
                               v-on:previewFile="this.docLink= $event"/>

            <br/>
            <message v-bind="result"></message>
            <br/>
        </div>
        <object class='col' height="500px" type="application/pdf" v-bind:data="docLink" width="100%"/>

        <!--        <object class='col' data="/img/getbill-7.pdf" type="application/pdf" width="100%" height="500px"/>-->
    </div>
    </div>
</template>


<script>

export default {
    data() {
        return {
            // files: [{file: null, description:'academic_card',link:'',id:0,message:null,success:null}],
            result: {
                message: 'ready',
                success: true,
                hide: false,
                errors: {
                    default: function () {
                        return [];
                    }
                }
            },
            docLink: '',
            cardApplication: null,
            commentStudent: null
        }
    },
    computed: {
        status: function () {
            return this.cardApplication ? this.cardApplication.card_last_update.status : null;
        },
        expiration_date: function () {
            return this.cardApplication ? this.cardApplication.expiration_date : null;
        },
        applicationEdit() {
            return [
                this.$enums.CardStatusEnum.INCOMPLETE,
                this.$enums.CardStatusEnum.SUBMITTED,
                this.$enums.CardStatusEnum.TEMPORARY_SAVED,
            ].includes(this.status);
        },
        title() {
            return document.title = (this.applicationEdit) ? 'card Edit' : 'card Show'
        }
    },
    methods: {
        broadcasting() {
            if (typeof Echo !== 'undefined')
                Echo.private(`cardApplication.${this.cardApplication.id}`)
                    .listen('CardApplicationUpdated', (e) => {
                        this.cardApplication.expiration_date = e['expiration_date'];
                        this.cardApplication.card_last_update.status = e['status'];

                    });
        },
        startingData() {
            this.getApplication();
        },
        getApplication() {

            let vue = this;
            let url = route('cardApplication.index');
            console.log('getApplication');
            axios.get(url
            ).then(responseJson => {
                let json = responseJson['data'];
                this.cardApplication = json['cardApplication'];

                this.broadcasting();
            }).catch(errors => {
                if (errors.response.status === 404)
                    return this.$router.push({name: 'card.application.create'});
                vue.result.message = 'Retrieving application has failed :'
                vue.result.errors = errors;
                vue.result.success = false;
            });

        },
        async submit_form() {
            let vue = this;
            let url = route('cardApplication.update', this.cardApplication);
            let params = new FormData();
            vue.result.message = ''; //#todo more clever way to show if the value is the same
            if (!this.applicationEdit) {
                vue.result.success = false;
                vue.result.message = "The status of the application do not give you the ability to submit "
            }

            if (!(await this.$refs.CardDocuments.submitFiles())) {
                vue.result.success = false;
                vue.result.message = 'some files has not uploaded or delete on the server your application status will not change'
                return;
            }
            params.append('_method', 'PUT');
            if (vue.commentStudent) {
                params.append('comment', vue.commentStudent);
            }
            console.log('start axios to application for submition')
            axios.post(url, params
            ).then(function (responseJson) {
                let json = responseJson['data'];
                vue.result.success = json['success'];
                vue.result.message = json['message'];
            }).catch(function (errors) {
                vue.result.success = false;
                vue.result.errors = errors.response.data.errors;
                vue.result.message = "Request failed: your status hasn't change";
            }).finally(function () {
                if (vue.result.success) {
                    vue.cardApplication.card_last_update.status = vue.$enums.CardStatusEnum.SUBMITTED;
                }

                }
            )
        }
    }
    ,
    created() {
        this.startingData();
        document.title = this.title;
    },
}
</script>

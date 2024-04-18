<template>
    <div v-if="application" class='row'>
        <div class="col-xm-12 col-sm-6 col-md-7 col-lg-8">
            <header>
                <br/>
                <h4 class="text-left">Application : {{ application.id }}</h4>
            </header>
            <h5>files</h5>
            <select v-model="selectFile">
                <!--            <option disabled value="">Please select one</option>-->
                <option v-for="file in files" :value="file"> files ; {{ file.id }}></option>
            </select>
            <select v-if="selectFile" v-model="selectFile.status" v-on:select="updateStatus(selectFile.status)">
                <option disabled value="">Please select one</option>
                // 'submitted','accepted','rejected','incomplete'
                <option v-for="(value, status) in $enums.CardDocumentStatusEnum"
                        :key="'CardDocumentStatusEnum.'+status+selectFile.id"
                        :value="value"> {{ status }}
                </option>
            </select>
            <message v-bind="resultFile"></message>
            <object v-if="selectFile" class='col' height="500px" type="application/pdf"
                    v-bind:data="selectedFileUrl" width="100%"/>
        </div>

        <!--            <router-view  v-bind:application="selectedItem"/>-->
        <div class="col-auto">
            <h4>Application Status</h4>
            <div>
                <label for="commentStaff">Enter text:</label>
                <input id="commentStaff" v-model="commentChecking" type="text">
                <label for="expiration_date">Enter text:</label>
                <input id="expiration_date" v-model="expirationDate" type="date">

            </div>
            <select v-model="application.status" v-on:change="updateApplicationStatus(application)">
                <option disabled value="">Please select one</option>
                <option
                    v-for="status in ['ACCEPTED','REJECTED','INCOMPLETE']"
                    :key="'CardStatusEnum.'+status" :value="$enums.CardStatusEnum[status]"> {{ status }}
                </option>
            </select>
            <message v-bind="result"></message>
        </div>
    </div>

</template>

<script>
export default {
    props: {
        application: Object
    },
    data() {
        return {
            resultFile: {
                message: '',
                success: null,
                hide: true,
                errors: []
            },
            currentStatus: null,
            currentFileStatus: null,
            selectFile: null,
            files: [],
            commentChecking: null,
            expirationDate: null,
            result: {
                message: '',
                success: true,
                hide: false,
                errors: ['']
            },
        }
    },
    methods: {
        startingData() {
            this.currentStatus = this.application.card_last_update.status;
            this.files = this.application.card_application_document;
        },
        async updateStatus(file) {
            let params = new FormData();
            let vue = this;
            let url = route('document.update', {'document': file.id});
            vue.resultFile.message = ''; //#todo more clever way to show if the value is the same
            params.append('_method', 'PUT')
            // params.append(`id`, file.id);
            params.append(`status`, file.status);
            return await axios.post(url, params
            ).then(function (responseJson) {
                let json = responseJson['data'];
                vue.resultFile.success = json['success'];
                vue.resultFile.message = json['message'];
                vue.resultFile.errors = []
                return json['success'];
            }).catch(function (errors) {
                vue.resultFile.success = false;
                vue.resultFile.errors = errors.response.data.errors
                vue.resultFile.message = "Request failed:";
                return false;
            });
        },
        updateApplicationStatus(application) {
            let params = new FormData();
            let vue = this;
            //params.append('_method','PUT')
            // params.append(`id`, application.id);
            params.append(`status`, application.status);
            params.append('card_application_id', application.id)
            if (this.expirationDate) {
                params.append('expiration_date', this.expirationDate)
            }
            if (this.commentChecking) {
                params.append('card_application_staff_comment', this.commentChecking)
            }
            console.log(params);
            axios.post(route('cardApplication.checking.store', {'category': application.status}), params
            ).then(function (responseJson) {
                let json = responseJson['data'];
                // application.success = json['success'];
                //  application.message = json['message'];
                //application.message='the application is not exist or is ureadable please upload a new one';
                vue.result.success = json == 1;
                vue.result.errors = [];
            }).catch(function (errors) {
                vue.result.errors = errors.response.data.errors;
                vue.result.success = false
            }).finally(() => {
                if (vue.result.success) {
                    vue.result.message = "Change from " + vue.currentStatus + ' to ' + application.status;
                    vue.currentStatus = application.status;
                    vue.updateApplicationsIds({
                        'cardApplication_id': application.id,
                        status: application.status
                    })
                    return;
                }//else
                application.status = vue.currentStatus;
                vue.result.message = "Request failed:";
            });
        },
    },
    computed: {
        selectedFileUrl() {
            return route('document.show', {'document': this.selectFile?.id});
        }
    },
    watch: {
        application(newValue) {
            console.log(this.application);
            if (newValue)
                this.startingData();
            this.selectFile = null;
            console.log(this.application);
        },
        async selectFile(newValue, oldValue) {
            if (oldValue)
                if (oldValue.status != this.currentFileStatus) if (!await this.updateStatus(oldValue))
                    oldValue.status = this.currentFileStatus;
            this.currentFileStatus = newValue ? newValue.status : null;
        }
    },
    created() {
        if (this.application) this.startingData();
    }
}
</script>

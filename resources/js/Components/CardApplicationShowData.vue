<template>
    <div class="col-xm-12 col-sm-6 col-md-7 col-lg-8">
        <header>
            <br/>
            <h4 class="text-left">Application : {{ applicationId }}</h4>
        </header>
        <h5>files</h5>
        <select v-model="selectFile">
            <!--            <option disabled value="">Please select one</option>-->
            <option v-for="file in files" :value="file"> files ; {{ file.id }}></option>
        </select>
        <select v-model="selectFile.status" v-on:select="updateStatus(selectFile.status)">
            <option disabled value="">Please select one</option>
            // 'submitted','accepted','rejected','incomplete'
            <option v-for="(value, status) in $enums.CardDocumentStatusEnum"
                    :key="'CardDocumentStatusEnum.'+status+selectFile.id"
                    :value="value"> {{ status }}
            </option>
        </select>
        <message v-bind="result"></message>
        <object v-if="selectFile" class='col' height="500px" type="application/pdf"
                v-bind:data="selectedFileUrl" width="100%"/>
    </div>

</template>

<script>
export default {
    props: {
        applicationId: Number
    },
    data() {
        return {
            result: {
                message: '',
                success: null,
                hide: true,
                errors: []
            },
            currentStatus: null,
            selectFile: '',
            files: [],
        }
    },
    methods: {
        startingData() {
            let vue = this;
            let url = route('document.index', {'cardApplication': this.applicationId});
            console.log('startingData');

            axios.get(url
            ).then(function (responseJson) {
                let json = responseJson['data'];
                vue.files = json;
            }).catch(function (errors) {
                vue.result.success = false;
                vue.result.message = 'Retrieving files of this application has failed :'
                vue.result.errors = errors.response.data.errors;

                /*for (let error in errors.response.data.errors) {
                    form.result = form.result + ' ' + error + ' => ' + errors.response.data.errors[error];
                    console.log(errors.response.data.errors[error])
                }*/
            });

        },
        updateStatus(file) {
            let params = new FormData();
            let vue = this;
            let url = route('document.update', {'cardApplication': this.applicationId, 'document': file.id});
            vue.result.message = ''; //#todo more clever way to show if the value is the same
            params.append('_method', 'PUT')
            // params.append(`id`, file.id);
            params.append(`status`, file.status);
            axios.post(url, params
            ).then(function (responseJson) {
                let json = responseJson['data'];
                vue.result.success = json['success'];
                vue.result.message = json['message'];
                vue.result.errors = []
            }).catch(function (errors) {
                vue.result.success = false;
                vue.result.errors = errors.response.data.errors
                vue.result.message = "Request failed:";
            });
        }
    },
    computed: {
        selectedFileUrl() {
            return route('document.show', {'cardApplication': this.applicationId, 'document': this.selectFile.id});
        }
    },
    watch: {
        applicationId(newValue) {
            this.startingData();
        },
        selectFile(newValue, oldValue) {
            if (oldValue)
                if (oldValue.status != this.currentStatus)
                    if (!this.updateStatus(oldValue))
                        oldValue.status = this.currentStatus;
            this.currentStatus = newValue.status;
        }
    },
    created() {
        this.startingData();
    }
}
</script>

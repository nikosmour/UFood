<template>
    <div class='row '>
        <div class='col-5'>
            <header>
                <br/>
                <h4 class="text-left">card edit</h4>
            </header>
            <form id="card_application_form" v-on:submit.prevent="">
                <button @click="addFileUpload()">Add File</button>
                <div v-for="(file, index) in files" :key="index">
                    <input v-if="canEditDocument[index]" accept="application/pdf" type="file"
                           v-bind:disabled="!file.description" @change="onFileChange($event, index)">
                    <input v-model="file.description" placeholder="Description" type="text"
                           v-bind:disabled="!canEditDocument[index]">
                    <button @click="previewFile($event,index)">preview {{ file.id }}</button>
                    <message v-bind="file.result"></message>
                    <!--                    <label v-if="file.result.message || file.status">{{ file.status + '  ' + file.result.message }}</label>-->
                </div>
                <br/>
                <message v-bind="result"></message>
                <br/>
                <button v-if="applicationEdit" class="btn btn-primary" type="submit" @click="submit_form">Submit
                </button>
            </form>
        </div>
        <object class='col' height="500px" type="application/pdf" v-bind:data="docLink" width="100%"/>

        <!--        <object class='col' data="/img/getbill-7.pdf" type="application/pdf" width="100%" height="500px"/>-->
    </div>
</template>


<script>
export default {
    props: {
        url: String,
        // urlDoc:String,
        docFiles: Array,
        applicationEdit: Boolean
    },
    data() {
        return {
            // files: [{file: null, description:'academic_card',link:'',id:0,message:null,success:null}],
            result: {
                message: this.url,
                success: true,
                hide: false,
                errors: []
            },
            docLink: '',
            files: [],
            urlDoc: this.url + '/document'
        }
    },
    computed: {
        canEditDocument: function () {
            return this.files.map((file, index) => {
                return this.applicationEdit && ['incomplete', null, 'submitted'].includes(file.status)
            });

        }
    },
    methods: {
        startingData() {
            this.docFiles.forEach((file, index) => {
                this.addFileUpload(null, file.id, file.description, this.urlDoc + '/' + file.id, file.status);
            });
            if (0 == this.files.length)
                this.addFileUpload();
            console.log(this.files);

        },
        addFileUpload(file = null, id = 0, description = '', link = '', status = null, message = '', success = null) {
            this.files.push({
                file: file,
                id: id,
                description: description,
                link: link,
                status: status,
                result: {
                    message: message,
                    success: success,
                    hide: false,
                    errors: []
                },
            });
        },
        onFileChange(event, index) {
            let file = this.files[index];
            file.file = event.target.files[0];
            if (!file.file || !file.description)
                return file.message = 'there isn\'t any file or description';
            file.id = 0; //so the upload will know that has been added a new file
            this.fileUpload(file);
        },
        fileUpload(file) {
            if (0 > file.id) {
                file.success = true;
                file.message = 'the file has already uploaded';
                return;
            }
            let params = new FormData();
            params.append(`file`, file.file);
            params.append(`description`, file.description);
            axios.post(this.urlDoc, params
            ).then(function (responseJson) {
                let json = responseJson['data'];
                file.id = json['id'];
                file.result.success = json['success'];
                file.result.message = json['message'];
                file.result.errors = [];
            }).catch(function (errors) {
                file.result.errors = errors.response.data.errors;
                file.result.success = false;
                file.result.message = "Request failed:";
            }).finally(function () {
                file.link = '';
                file.status = file.result.success ? 'submitted' : 'not uploaded';
                file.result.message += file.status;
            });
        },
        previewFile(event, index) {
            const file = this.files[index];
            if ('' != file.link) return (this.docLink = file.link);
            if (null == file.file) return;
            const reader = new FileReader();
            reader.onload = (e) => {
                this.docLink = file.link = e.target.result;
            };
            reader.readAsDataURL(file.file);
        },
        submit_form() {
            let vue = this;
            vue.result.message = ''; //#todo more clever way to show if the value is the same
            this.files.forEach((file, index) => {
                this.fileUpload(file);
            });
            let params = new FormData();
            params.append('_method', 'PUT');
            axios.post(vue.url, params
            ).then(function (responseJson) {
                let json = responseJson['data'];
                vue.result.success = json['success'];
                vue.result.message = json['message'];
            }).catch(function (errors) {
                vue.result.success = false;
                vue.result.errors = errors.response.data.errors;
                vue.result.message = "Request failed:";
            });
        }
    },
    created() {
        this.startingData();
    }
}
</script>

<template>
    <div class='row '>
        <div class='col-5'>
            <header>
                <br/>
                <h4 class="text-left">card edit</h4>
            </header>
            <form v-if="applicationEdit" id="card_application_form" v-on:submit.prevent="">
                <button @click="addFileUpload()">Add File</button>
                <div v-for="(file, index) in files" :key="index">
                    <input v-if="canEditDocument[index]" accept="application/pdf" type="file"
                           v-bind:disabled="!file.description" @change="onFileChange($event, index)">
                    <input v-model="file.description" placeholder="Description" type="text"
                           v-bind:disabled="!canEditDocument[index]">
                    <button @click="previewFile($event,index)">preview {{ file.id }}</button>
                    <button v-if="0 !=file.id" @click="file.status = 'to delete'">delete {{ file.id }}</button>
                    <button v-else @click="files.splice(index, 1);">cancel adding file</button>
                    <message v-bind="file.result"></message>
                    <!--                    <label v-if="file.result.message || file.status">{{ file.status + '  ' + file.result.message }}</label>-->
                </div>

                <button v-if="applicationEdit" class="btn btn-primary" type="submit" @click="submit_form">Submit
                </button>
            </form>
            <div v-else>
                <div v-for="(file, index) in files" :key="index">
                    <input v-model="file.description" placeholder="Description" type="text"
                           v-bind:disabled="!canEditDocument[index]">
                    <button @click="previewFile($event,index)">preview {{ file.id }}</button>
                    <message v-bind="file.result"></message>
                    <!--                    <label v-if="file.result.message || file.status">{{ file.status + '  ' + file.result.message }}</label>-->
                </div>
            </div>
            <br/>
            <message v-bind="result"></message>
            <br/>
        </div>
        <object class='col' height="500px" type="application/pdf" v-bind:data="docLink" width="100%"/>

        <!--        <object class='col' data="/img/getbill-7.pdf" type="application/pdf" width="100%" height="500px"/>-->
    </div>
</template>


<script>
export default {
    props: {
        cardApplication: Object,
        applicationEdit: Boolean
    },
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
            docFiles: [],
            files: [],
        }
    },
    computed: {
        canEditDocument: function () {
            return this.files.map((file, index) => {
                return ['incomplete', null, 'submitted'].includes(file.status)
            });

        }
    },
    methods: {
        startingData() {
            let vue = this;
            let url = route('document.index', {'cardApplication': this.cardApplication.id});
            console.log('startingData');
            axios.get(url
            ).then(function (responseJson) {
                let json = responseJson['data'];
                vue.docFiles = json;
                json.forEach((file, index) => {
                    vue.addFileUpload(null, file.status, file.description, file.id, route('document.show', {
                        'cardApplication': vue.cardApplication,
                        'document': file.id
                    }));
                });
                if (0 == json.length)
                    vue.addFileUpload();
                console.log(vue.files);
            }).catch(function (errors) {
                vue.result.success = false;
                vue.result.message = 'Retrieving files of this application has failed :'
                vue.result.errors = errors.response.data.errors;
            })

        },
        addFileUpload(file = null, status = null, description = '', id = 0, link = '', message = '', success = null) {
            return this.files.push({
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

            if (0 == file.id)
                return;
            file.id = 0;
            this.files.push(file);// has been added the new file
            let oldFile = this.files[index] = this.docFiles[index]; //restore the old file
            oldFile.status = (!confirm('would you like to keep the old file? if yes you will see the new file on the end')) ? "to delete" : (oldFile.status != 'incomplete') ? oldFile.status : 'submitted';


        },
        fileUpload(file, index) {
            let params = new FormData();
            let url;
            console.log(index, file.id, 0 > file.id);
            file.result.message = ''; //#todo more clever way to show if the value is the same
            //is it need to delete the file;
            if ('to delete' == file.status) {
                url = route('document.destroy', {'cardApplication': this.cardApplication, 'document': file.id})
                params.append('_method', 'DELETE');
            } else if (0 == file.id) {// submit a new  file
                if (file.file != null) {
                    params.append(`file`, file.file);
                    params.append(`description`, file.description)
                    url = route('document.store', {'cardApplication': this.cardApplication})
                } else {
                    file.result.message = 'there is not file to upload';
                    return file.result.success = true;
                }
            } else if (file.description != this.docFiles[index].description) { //update existing file description
                url = route('document.update', {'cardApplication': this.cardApplication, 'document': file.id})
                params.append(`description`, file.description);
                params.append('_method', 'PUT');
            } else { // there is a file but nothing changed
                file.result.message = 'the file has already uploaded';
                return file.result.success = true;
            }
            return axios.post(url, params
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
                file.status = file.result.success ? 'submitted/deleted' : 'not uploaded';
                file.result.message += file.status;
                return file.result.success;
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
            let url = route('cardApplication.update', this.cardApplication);
            let successFilesUpload = true;
            let params = new FormData();
            vue.result.message = ''; //#todo more clever way to show if the value is the same
            this.files.forEach((file, index) => {
                this.fileUpload(file, index);
                successFilesUpload = successFilesUpload && file.result.success;

            });
            if (!successFilesUpload) {
                vue.result.success = false;
                vue.result.message = 'some files has not uploaded or delete on the server your application status will not change'
                return;
            }
            params.append('_method', 'PUT');
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
                        let time = 3000;
                        setTimeout(() => location.reload(true), time);
                        vue.result.message += "and the page will reload in " + time + 'ms';
                    }

                }
            )
        }
    }
    ,
    created() {
        this.startingData();
    }
}
</script>

<template>
    <div class=' container-fluid row '>
        <form action="" v-on:submit.prevent="">
            <label>Application Id :<input v-model="search.application_id" min="1" type="number"/></label>
            <label>Academic Id :<input v-model="search.academic_id" min="1" type="number"/></label>
            <label>Arithos mitrou :<input v-model="search.a_m" min="1" type="number"/></label>
            <label>email : <input v-model="search.email" type="email"/></label>
            <button class="btn btn-primary" type="submit" @click="getId">Submit</button>
        </form>
        <table class="col-auto ">
            <thead>
            <tr>
                <th>ID</th>
            </tr>
            </thead>
            <tbody>
            <tr v-for="item in applications" :key="item.id" @click="showSecondTable(item)">
                <td>{{ item.id }}</td>
            </tr>
            </tbody>
        </table>
        <div v-if="selectedItem !== null" class='col-auto row'>
            <CardApplicationShowData v-bind:application="selectedItem"/>
            <div class="col-auto">
                <h4>Application Status</h4>
                <div>
                    <label for="commentStaff">Enter text:</label>
                    <input id="commentStaff" v-model="commentChecking" type="text">
                    <label for="expiration_date">Enter text:</label>
                    <input id="expiration_date" v-model="expirationDate" type="date">

                </div>
                <select v-model="selectedItem.status" v-on:change="updateStatus(selectedItem)">
                    <option disabled value="">Please select one</option>
                    <option
                        v-for="status in ['ACCEPTED','REJECTED','INCOMPLETE']"
                        :key="'CardStatusEnum.'+status" :value="$enums.CardStatusEnum[status]"> {{ status }}
                    </option>
                </select>
                <message v-bind="result"></message>
            </div>
        </div>
    </div>
</template>


<script>

export default {
    data() {
        return {
            search: {
                application_id: null,
                academic_id: null,
                email: null,
                a_m: null,
            },
            selectedItem: null,
            commentChecking: null,
            expirationDate: null,
            currentStatus: null,
            result: {
                message: '',
                success: true,
                hide: false,
                errors: ['']
            },
            applications: [],
            category: this.$route.params.category,
        };

    },
    methods: {
        broadcasting() {
            if (typeof Echo !== 'undefined' && typeof this.category !== 'undefined')
                Echo.join(`cardChecking.${this.category}`)
                    .here((users) => {
                        console.log('i am on  the channel', users);
                    })
                    .joining((user) => {
                        console.log('joining', user);
                    })
                    .leaving((user) => {
                        console.log('leaving ', user);
                    })
                    .error((error) => {
                        console.error(error);
                    })
                    .listen('CardApplicationUpdated', this.updateApplicationsIds);
        },
        getId() {
            let promise;
            if (this.search.application_id)
                promise = this.getApplications('application_id', this.search.application_id);
            else if (this.search.academic_id)
                promise = this.getApplications('academic_id', this.search.academic_id);
            else if (this.search.a_m)
                promise = this.getApplications('a_m', this.search.a_m);
            else if (this.search.email)
                promise = this.getApplications('email', this.search.email);
            else
                promise = this.getApplications('status', this.category);
            promise.then(applications => {
                this.applications = applications;
            })
        },
        async getApplications(name, value) {
            let params = new FormData();
            let vue = this;
            params.append(name, value);
            return await axios.post(route('cardApplication.checking.search'), params
            ).then(function (responseJson) {
                let applications = responseJson['data'];
                let success = applications.length > 0;
                if (success && name === 'application_id') {
                    vue.selectedItem = applications[0];
                    vue.currentStatus = applications[0].card_last_update.status;
                }
                vue.result.success = success;
                vue.result.errors = [];
                if (success) {
                    vue.result.message = "Applications found";
                    return applications;
                }
                //else
                vue.selectedItem = vue.currentStatus = null;
                vue.result.message = "Request failed: Application don't found";
                return [];
            }).catch(function (errors) {
                console.log(errors);
                // vue.result.errors = errors.response.data.errors;
                vue.result.message = "Request failed: Application don't found";
                vue.result.success = false;
                return [];
            });

        },


        async startingData() {
            console.log('cardApplicationChecking.startingData')
            this.applications = await this.getApplications('status', this.category);
        },
        showSecondTable(item) {
            console.log('showSecondTable');
            this.getApplications('application_id', item.id);
        },
        updateStatus(application) {
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
        updateApplicationsIds(e) {
            let cardApplication_id = e.cardApplication_id;
            let status = e.status;
            let position = this.applications.findIndex(obj => obj.id >= cardApplication_id);
            if (position != -1)
                if (status != this.category)//&& this.applications[position].id === cardApplication_id)
                    this.applications.splice(position, 1);
                else
                    this.applications.splice(position, 0, {id: cardApplication_id});
            else if (status === this.category)
                this.applications.push({id: cardApplication_id});
        }

    },
    created() {
        this.startingData();
        this.broadcasting();

    },
}
</script>

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
                <td>
                    <router-link :to="{name:'cardApplicationChecking.application',params:{application:item.id}}"
                                 class="nav-link router-link-exact-active" replace>{{ item.id }}
                    </router-link>
                </td>
            </tr>
            </tbody>
        </table>
        <CardApplicationShowData class="col-auto" v-bind:application="selectedItem"/>
        <message v-bind="result"></message>
    </div>
</template>


<script>

import CardApplicationShowData from "../Components/CardApplicationShowData.vue";

export default {
    components: {CardApplicationShowData},
    data() {
        return {
            search: {
                application_id: null,
                academic_id: null,
                email: null,
                a_m: null,
            },
            selectedItem: null,
            result: {
                message: '',
                success: true,
                hide: false,
                errors: ['']
            },
            applications: [],
        };
    },
    computed: {
        category() {
            return this.$enums.CardStatusEnum[this.$route.params.category.toUpperCase()];
        },
        applicationId() {
            return this.$route.params.application || this.$route.query.application;
        },
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
                vue.result.success = success;
                vue.result.errors = [];
                if (success) {
                    vue.result.message = "Applications found";
                    return applications;
                }
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
            this.getApplications('status', this.category).then(
                applications => {
                    return this.applications = applications;
                }
            );

            this.selectedItem = this.applicationId ? (await this.getApplications('application_id', this.applicationId))[0] : null;


        },
        showSecondTable(item) {
            console.log('showSecondTable');
            // this.getApplications('application_id', item.id);
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
    mounted() {
        this.startingData();
        this.broadcasting();

    },
    watch: {
        category(newValue, oldValue) {
            this.startingData()
            this.broadcasting();
            if (typeof Echo !== 'undefined' && typeof oldValue !== 'undefined')
                Echo.leave(`cardChecking.${oldValue}`);
        },
        async applicationId(newValue) {
            this.selectedItem = newValue ? (await this.getApplications('application_id', newValue))[0] : null;
        }

    },
    beforeRouteLeave(from, to) {
        if (typeof Echo !== 'undefined' && typeof this.category !== 'undefined')
            Echo.leave(`cardChecking.${this.category}`);
    },
}
</script>

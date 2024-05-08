<template>
    <div>
        <form action="" v-on:submit.prevent="getId">
            <label>Application Id :<input v-model="search.application_id" min="1" type="number"/></label>
            <label>Academic Id :<input v-model="search.academic_id" min="1" type="number"/></label>
            <label>Arithos mitrou :<input v-model="search.a_m" min="1" type="number"/></label>
            <label>email : <input v-model="search.email" type="email"/></label>
            <button class="btn btn-primary" type="submit">Submit</button>
        </form>
        <table><!--class="col-auto >-->
            <thead>
            <tr>
                <th>ID</th>
            </tr>
            </thead>
            <tbody>
            <tr v-for="item in applications" :key="item.id">
                <td>
                    <router-link :to="{name:this.$route.name,query:{application:item.id}}"
                                 class="nav-link router-link-exact-active" replace>{{ item.id }}
                    </router-link>
                </td>
            </tr>
            </tbody>
        </table>
    </div>
</template>


<script>
export default {
    props: {
        applications: Array
    },
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
            // applications: [],
        };
    },
    /*computed: {
        category() {
            return this.$enums.CardStatusEnum[this.$route.params.category.toUpperCase()];
        },
        applicationId() {
            return this.$route.params.application || this.$route.query.application;
        },
    },*/
    methods: {
        /*broadcasting() {
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
        },*/
        getId() {
            if (this.search.application_id)
                this.$router.replace({
                    name: this.$route.name,
                    query: {'application': this.search.application_id}
                });
            else if (this.search.academic_id)
                this.$emit('getId', ['academic_id', this.search.academic_id]);
            else if (this.search.a_m)
                this.$emit('getId', ['a_m', this.search.a_m]);
            else if (this.search.email)
                this.$emit('getId', ['email', this.search.email]);
            else
                this.$emit('getId', ['status', this.category]);
        },
    }
    /*async getApplications(name, value) {
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
    updateApplicationsIds(e) {
        let cardApplication_id = e.cardApplication_id;
        let status = e.status;
        let position = this.applications.findIndex(obj => obj.id >= cardApplication_id);
        if (position !== -1)
            if (status !== this.category)//&& this.applications[position].id === cardApplication_id)
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
},*/
}
</script>

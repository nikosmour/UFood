<template>
    <div class=' container-fluid row '>
        <router-view :applications="applications" class=' col ' v-on:getId="getId($event)"/>
        <template v-if="true" class="col-auto">
            <!--            <b-pagination
                            v-model="currentPage"
                            total-rows=100
                            per-page=1
                            first-text="⏮"
                            prev-text="⏪"
                            next-text="⏩"
                            last-text="⏭"
                            class="mt-4"
                        ></b-pagination>-->
            <div v-if="cursor.data">
                <button v-if="cursor.next_cursor" @click="nextPage">Next Page</button>
                <button v-if="cursor.prev_cursor" @click="prevPage">Previous Page</button>
            </div>
            <CardApplicationShowData v-bind:application="selectedItem"/>
        </template>
        <message v-bind="result"></message>
    </div>
</template>


<script>

import CardApplicationShowData from "../Components/CardApplicationShowData.vue";

export default {
    components: {CardApplicationShowData},
    data() {
        return {
            cursor: {data: []},
            selectedItem: null,
            result: {
                message: '',
                success: true,
                hide: false,
                errors: ['']
            },
        };
    },
    computed: {
        category() {
            return this.$route.params.category ? this.$enums.CardStatusEnum[this.$route.params.category.toUpperCase()] : null;
        },
        applicationId() {
            return this.$route.params.application || this.$route.query.application;
        },
        applications() {
            return this.cursor.data;
        }
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
        getId(formData) {
            this.getApplications(formData[0], formData[1]).then(applications => {
                this.$router.replace({name: this.$route.name, query: {'application': applications[0].id}})


            })
        },
        async getApplications(name, value, url = route('cardApplication.checking.search')) {
            let params = {[name]: value};
            let vue = this;

            return await axios.get(url, {params: params}
            ).then(responseJson => {
                console.log('get Application', responseJson['data']);
                if (name !== 'application_id')
                    this.cursor = responseJson['data'];
                let applications = responseJson['data']['data'];
                let success = applications.length > 0;
                vue.result.success = success;
                vue.result.errors = [];
                if (success) {
                    vue.result.message = "Applications found";
                    return applications;
                }
                vue.result.message = "Request failed: Application don't found";
                return [null];
            }).catch(function (errors) {
                console.log(errors);
                // vue.result.errors = errors.response.data.errors;
                vue.result.message = "Request failed: Application don't found";
                vue.result.success = false;
                return [null];
            });

        },


        async startingData() {
            console.log('cardApplicationChecking.startingData')
            if (this.category)
                this.getApplications('status', this.category).then(
                    applications => {
                        if (!this.applicationId && applications.length > 0 && applications[0] !== null)
                            this.$router.replace({name: this.$route.name, query: {'application': applications[0].id}})
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
        },
        nextPage() {
            this.changePage(this.cursor?.next_page_url); // Use the next cursor from the response
        },

        prevPage() {
            this.changePage(this.cursor?.prev_page_url);// Use the prev cursor from the response
        },
        changePage(url) {
            if (url)
                this.getApplications('status', this.category, url).then(applications => {
                    this.$router.replace({name: this.$route.name, query: {'application': applications[0]?.id}});
                });
        },


    },
    mounted() {
        this.startingData();
        this.broadcasting();

    },
    watch: {
        category(newValue, oldValue) {
            this.applications = []
            this.startingData()
            this.broadcasting();
            if (typeof Echo !== 'undefined' && typeof oldValue !== 'undefined')
                Echo.leave(`cardChecking.${oldValue}`);
        },
        async applicationId(newValue) {
            if (!newValue)
                return
            let position = this.applications.findIndex(obj => obj.id == newValue);
            this.selectedItem = position === -1 ? (await this.getApplications('application_id', newValue))[0] : this.applications[position];
        }

    },
    beforeRouteLeave(from, to) {
        if (typeof Echo !== 'undefined' && typeof this.category !== 'undefined')
            Echo.leave(`cardChecking.${this.category}`);
    },
}
</script>

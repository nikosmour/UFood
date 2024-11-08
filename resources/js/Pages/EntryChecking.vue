<template>
    <v-row class="h-75">
        <v-col class="h-100" cols="12" lg=8 md="6" sm="6">
            <entry-checking-form
                @newEntry="newEntry($event)"
            />
        </v-col>
        <v-col class="d-flex align-center" cols="12" lg="4" md="6" sm="6">
            <export-statistics-form :periods="['current meal', 'today', 'adapted']"
                                    :statistics="statistics" class="h-100"
            />
        </v-col>
    </v-row>
</template>

<script>
import ExportStatisticsForm from "../components/ExportStatisticsForm.vue";
import EntryCheckingForm from "../Components/EntryCheckingForm.vue";

export default {
    components: {
        EntryCheckingForm,
        ExportStatisticsForm
    },
    props: {
        statisticsServer: {
            type: Object,
        }

    },
    data: function () {
        return {
            statistics: null,
        }
    },
    methods: {
        async fetchData() {
            return await this.$axios.get(this.route('entryChecking.create')).then(
                response => {
                    return response.data
                }
            );
            // return {cards: 0, coupons: 0,}
        },
        newEntry(entryCategory) {
            this.statistics[entryCategory] += 1;
        },
    },
    async mounted() {
        this.statistics = this.statisticsServer || (await this.fetchData())
    },
}
</script>

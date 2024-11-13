<template>
    <v-row class="h-75">
        <v-col class="h-100" cols="12" lg="8" md="6" sm="6">
            <entry-checking-form @newEntry="handleNewEntry"/>
        </v-col>
        <v-col class="d-flex align-center" cols="12" lg="4" md="6" sm="6">
            <my-export-statistics-form
                :periods="['current meal', 'today', 'adapted']"
                :statistics="statistics"
                class="h-100"
            />
        </v-col>
    </v-row>
</template>

<script>
import MyExportStatisticsForm from "../components/MyExportStatisticsForm.vue";
import EntryCheckingForm from "./EntryChecking/EntryCheckingForm.vue";

export default {
    components: {
        EntryCheckingForm,
        MyExportStatisticsForm
    },
    props: {
        /**
         * Initial statistics data passed from the server.
         * This will be used to populate the statistics object if provided.
         */
        statisticsServer: {
            type: Object,
            required: false,
            default: null
        }
    },
    data() {
        return {
            /**
             * Local statistics data used for displaying counts in the export form.
             * This is initialized either from server data or fetched via API.
             */
            statistics: null
        };
    },
    methods: {
        /**
         * Fetches statistics data from the server if not provided via props.
         * @returns {Object} The statistics data from the server.
         */
        async fetchStatisticsData() {
            try {
                const response = await this.$axios.get(this.route('entryChecking.create'));
                return response.data;
            } catch (error) {
                console.error('Failed to fetch statistics data:', error);
                return {};
            }
        },

        /**
         * Updates the statistics count for a specific entry category.
         * This method is triggered by the EntryCheckingForm component.
         * @param {String} entryCategory - The category of the new entry to increment.
         */
        handleNewEntry(entryCategory) {
            if (this.statistics && this.statistics[entryCategory] !== undefined) {
                this.statistics[entryCategory] += 1;
            } else {
                console.warn(`Invalid entry category: ${entryCategory}`);
            }
        }
    },
    async mounted() {
        /**
         * Initialize statistics data on component mount.
         * If statistics data is provided via props, it is used directly;
         * otherwise, data is fetched from the server.
         */
        this.statistics = this.statisticsServer || await this.fetchStatisticsData();
    }
};
</script>

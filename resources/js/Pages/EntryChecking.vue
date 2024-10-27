<template>
    <div class="row h-100">
        <entry-checking-form @newEntry="newEntry($event)"></entry-checking-form>
        <export-statistics-form v-if="statistics" :statistics="statistics"></export-statistics-form>
    </div>
</template>

<script>
export default {
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
        newEntry(entryCategory) {
            this.statistics[entryCategory] += 1;
        },
        async fetchData() {
            return await this.$axios.get(this.route('entryChecking.create')).then(
                response => {
                    return response.data
                }
            );
            // return {cards: 0, coupons: 0,}
        }
    },
    async mounted() {
        this.statistics = this.statisticsServer || (await this.fetchData())
    },
}
</script>

<style scoped>
/* Ensure responsive design */
.row.my_flex_height {
    display: flex;
    flex-direction: column;
}

@media (min-width: 768px) {
    .row.my_flex_height {
        flex-direction: row;
    }
}
</style>

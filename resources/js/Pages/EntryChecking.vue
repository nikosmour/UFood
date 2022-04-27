<template>
    <div class="row my_flex_height"  >
        <entry-checking-form v-bind:url="urls.form" v-on:newEntry="newEntry($event)"></entry-checking-form>
        <export-statistics-form v-bind:url="urls.statistics" v-bind:show_free_food="true" v-bind:statistics="statistics" ></export-statistics-form>
    </div>
</template>

<script>
    export default {
        props:{
            urls: {
                type:Object,
                form:String,
                statistics:String
            },
            statisticsServer: {
                type: Object,
                cards: 0,
                coupons: 0,
            }

        },
        data:function(){
            return {
                statistics: this.statisticsServer
            }
        },
        methods:{
            newEntry(entryCategory){
                this.statistics[entryCategory]+=1;
            }
        }
    }
</script>

<template>
    <table class="table text-center table-hover table-col-to-row-sm caption-top">
        <caption>{{ caption }}</caption>
        <thead class="thead-dark">
        <tr>
            <th v-for="key in attributes" scope="col">{{ key }}</th>
        </tr>
        </thead>
        <tbody v-for="(model,modelKey) in models" :key="'model.'+modelKey">
        <tr>
            <td v-for="key in attributes" :key="'model.'+modelKey+'.attribute.'+key">
                {{ model[key] }}
            </td>
        </tr>
        <tr v-for="relationship in relationships" :key="'model.'+modelKey+'.attribute.'+relationship">
            <td/>
            <td :colspan="attributes.length -1">
                <models-to-table :caption="relationship" :models="dataToArray(model[relationship])"/>
            </td>
        </tr>
        </tbody>
    </table>
</template>

<script>
export default {
    props: {
        models: {
            /*type: Array,
            required: false,
            default: [],*/
        },
        caption: {
            type: String,
            default: "table",
        },
    },
    computed: {
        firstModel() {
            return this.models.length > 0 ? this.models[0] : {};
        },
        attributes() {
            return Object.keys(this.firstModel).filter(key => typeof this.firstModel[key] !== 'object');
        },
        relationships() {
            return Object.keys(this.firstModel).filter(key => typeof this.firstModel[key] === 'object');
        },


    },
    methods: {
        dataToArray(data) {
            return Array.isArray(data) ? data : [data];
        },
    },
};
</script>


<!--
<script>
export default {
    name: "modelsToTable",
    props:{
        models:Array,
        caption:{type:String,default:'table'}
    },
    data() {
        return {
            sizeModels:5
        }
    }
}
</script>


<template>
    <div>
        <table class="table text-center  table-hover table-col-to-row-sm caption-top">
            <caption>{{ caption }}</caption>
            <thead class="thead-dark" v-if="models.length !== 0">
                <tr>
                    <th scope="col" v-for="(value, key) in models[0] ">{{key}}</th>
                </tr>
            </thead>
            <tbody>
            <div v-for="model in models">
                <tr>
                    <td v-if="typeof value !== 'Object'" v-for="value in model">{{value}}</td>
                </tr>
                <tr v-if="typeof relationship === 'Object'" v-for="(relationship , name) in model">
                    <td></td>
                    <td colspan="{{sizeModels}}">
                        <models-to-table :models="relationship" :caption="name"/>
                    </td>
                </tr>
            </div>

&lt;!&ndash;            @foreach($model->getRelations() as $name=>$relation)
            @if(!is_null($relation))
            <tr>
                <td></td>
                <td colspan="{{count($model->getAttributes())-1}}">
                    @include('components.modelToTable',['models'=>(is_countable($relation))?$relation: [$relation],'caption'=>$name ])
                </td>
            </tr>
            @endif
            @endforeach
            @endforeach&ndash;&gt;
            </tbody>
        </table>
    </div>

</template>

<style scoped>

</style>
-->

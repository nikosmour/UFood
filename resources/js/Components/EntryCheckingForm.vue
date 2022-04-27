<template>
    <div class="col-xm-12 col-sm-6 col-md-7 col-lg-8">
        <header>
            <br/>
            <h4 class="text-left">Έλεγχος εισόδου</h4>
        </header>
        <form v-on:submit.prevent="check_id" id="use_form"> <!--method= "GET"-->
            <div class="form-group mx-auto " style="max-width:20em">
                <label  > <strong class="text-center">Αριθμός κάρτας</strong></label>
                <input id="academic_id" type="number" v-bind:class="getClass" v-model.number="academic_id" placeholder="Καταχωρίστε τον αριθμό της κάρτας" name="academic_id" autofocus required/>
                <label v-bind:class="getClass" v-if="show"> {{result}}</label>
            </div>
        </form>
    </div>
</template>

<script>
    export default {
        props:{url:String},
        data:function(){
            return{
                academic_id:'',
                success:true,
                result:this.url,
                show:false
            }
        },
        computed:{
            getClass: function () {
                return {
                    'text-success': this.success,
                    'text-danger': !this.success
                }
            }
        },
        methods:{
            hideResult(){
                setTimeout(()=>this.show = false, 2000)
            },
            check_id() {
                if (0 === this.academic_id )
                    return;
                let params = new FormData();
                params.append('academic_id', this.academic_id);
                let thiss = this;
                axios.post(this.url,params
                ).then(function (responseJson) {
                    let json=responseJson['data'];
                    thiss.success= json['pass'];
                    if(json['pass']) {
                        thiss.result = json['passWith'];
                        thiss.$emit('newEntry',json['passWith']+'s');
                    }
                    else
                        thiss.result=json;
                }).catch(function (errors) {
                    this.success = false;
                    console.log(errors.response.data.errors)
                    thiss.result = "Request failed:";
                    for( let error in errors.response.data.errors) {
                        thiss.result = thiss.result +' '+ error + ' => ' + errors.response.data.errors[error];
                        console.log(errors.response.data.errors[error])
                    }
                });
                this.show=true;
                this.hideResult()
            }
        }
    }
</script>

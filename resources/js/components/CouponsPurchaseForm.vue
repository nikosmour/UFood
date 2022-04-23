<template>
    <div>
        <header>
            <br/>
            <h4 class="text-left">Πώληση κουπονιών</h4>
        </header>
        <form  v-on:submit.prevent="coupons()">
            <div class="form-group mx-auto " style="max-width:20em">
                <div class="col-sm md-form">
                    <label > Αριθμός κάρτας</label>
                    <input  v-bind:class="getClass/*, {'form-control':true}*/ " type="number" v-model.number='academic_id'  placeholder="Καταχωρίστε τον αριθμό της κάρτας"  min="0" autofocus required/><!--name="academic_id"-->
                </div>
            </div>
            <div class="form-group form-row mx-auto  " style="max-width:30em">
                <div class="col-sm md-form">
                    <label >Πρωινό</label>
                    <input  class="form-control" type="number" v-model.number='breakfast'  value="0" min="0"/>
                </div>
                <div class="col-sm" >
                    <label >Μεσημεριανό</label>
                    <input class="form-control" type="number" v-model.number="lunch"  value="0" min="0"/>
                </div>
                <div class="col-sm">
                    <label >Βραδινό</label>
                    <input class="form-control" type="number" v-model.number="dinner"  value="0" min="0"/>
                </div>
            </div>
            <label v-bind:class="getClass" v-if="show"> {{result}}</label>
            <div class="mx-auto" style="max-width:5em">
                <button type="submit" class="btn btn-default" >Επόμενο</button>
            </div>
        </form>
    </div>
</template>

<script>
    export default {
        // mounted() {
        //     console.log('Component mounted.')
        // },
        props:{url:String},
        data:function(){
            return{
                academic_id:'',
                breakfast:0,
                lunch:0,
                dinner:0,
                success:true,
                result:'test',
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
                setTimeout(()=>this.show = false, 20000)
            },

            coupons() {
                if (0 == this.academic_id )
                    return;

                // if (0 ==this.breakfast  && 0 == this.lunch  && 0  == this.dinner)
                //     return;

                let params = new FormData();
                params.append('academic_id', this.academic_id);
                params.append('breakfast', this.breakfast);
                params.append('lunch', this.lunch);
                params.append('dinner', this.dinner);


                let thiss = this;
                if (confirm("Αγορά κουπονιών απο τον χρήστη με ακαδημαϊκή ταυτότητα: " + this.academic_id +
                    " Πρωινά: " + this.breakfast + " Γευμα: " + this.lunch + " Δείπνο: " + this.dinner))
                    axios.post(this.url,params
                    ).then(function (responseText) {
                        thiss.result = responseText['data'];

                    }).catch(function (errors) {
                        console.log(errors.response.data.errors)
                        thiss.result = "Request failed:";
                        for( let error in errors.response.data.errors) {
                            thiss.result = thiss.result +' '+ error + ' => ' + errors.response.data.errors[error];
                            console.log(errors.response.data.errors[error])
                        }
                    });
                if ("Επιτυχής πώληση" == this.result)
                    this.success = true;
                else
                    this.success = false;
                this.show=true;

                this.hideResult()



                    /*$.ajax({
                        url: this.url,
                        method: "POST",
                        processData: false,
                        contentType: false,
                        data: params,
                    }).done(function (responseText) {
                        thiss.result = responseText;
                        if ("Επιτυχής πώληση" == responseText)
                            thiss.success = true;
                        else
                            thiss.success = false;
                    }).fail(function (jqXHR, textStatus) {
                        thiss.result = "Request failed: " + textStatus;
                        thiss.success = false;
                    });*/


                    /*let xhttp = new XMLHttpRequest();
                    xhttp.onerror = function () {
                        alert(this.responseText);
                    } //

                    xhttp.onload = function () {
                        thiss.result = this.responseText;
                        if (thiss.result == "Επιτυχής πώληση")
                            thiss.success = true;
                        else
                            thiss.success = false;
                        // if (thiss.result == "Σφάλμα με την Βάση" || thiss.result == "Μη ορισμένος φοιτητής")
                        //     thiss.success=false;
                        // else if (thiss.result == "Επιτυχής πώληση"){
                        //     thiss.success=true;
                        // let temp=document.querySelector("#stoudents_koupons_numbers");
                        // let temp2=temp.innerHTML.split(" ");
                        // temp.innerHTML= (Number(temp2[0])+Number(breakfast.value))+" "+
                        //     (Number(temp2[1])+Number(lunch.value))+" "+
                        //     (Number(temp2[2])+Number(dinner.value));
                        // }
                        // alert(thiss.result);

                    };

                    if (confirm("Αγορά κουπονιών απο τον χρήστη με ακαδημαϊκή ταυτότητα: " + this.academic_id +
                        " Πρωινά: " + this.breakfast + " Γευμα: " + this.lunch + " Δείπνο: " + this.dinner)) {
                        $.ajaxSetup({
                            headers: {
                                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                            }
                        });
                        xhttp.open("POST", this.url, true);
                        xhttp.send(params);
                    }
                    //------------------------------------------------------------------------------------------------------------------

                    // let payload = {};

                    // send get request
                    // if (confirm("Αγορά κουπονιών απο τον χρήστη με ακαδημαϊκή ταυτότητα: " + this.academic_id +
                    //     " Πρωινά: " + this.breakfast+" Γευμα: " + this.lunch+" Δείπνο: " + this.dinner))
                    //
                    //     this.http.get(this.url, payload, function (data, status, request) {
                    //
                    //         // set data on vm
                    //         this.result = data;
                    //         if (data == "Επιτυχής πώληση")
                    //             this.success=true;
                    //         else
                    //             this.success=false;
                    //         alert(this.result);
                    //
                    //     }).error(function (data, status, request) {
                    //         // handle error
                    // });

                    // send get request
                       this.$http.get(url, payload, function (data, status, request) {

                           // set data on vm
                           this.response = data;

                       }).error(function (data, status, request) {
                           // handle error
                       });*/

            }

        }
    }
</script>

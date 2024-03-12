<template>
    <div class="col-xm-12 col-sm-6 col-md-7 col-lg-8">
        <header>
            <br/>
            <h4 class="text-left">Έλεγχος εισόδου</h4>
        </header>
        <form id="use_form" v-on:submit.prevent="check_id"> <!--method= "GET"-->
            <div class="form-group mx-auto " style="max-width:20em">
                <label> <strong class="text-center">Αριθμός κάρτας</strong></label>
                <input id="academic_id" v-model.number="academic_id" autofocus name="academic_id"
                       placeholder="Καταχωρίστε τον αριθμό της κάρτας" required type="number" v-bind:class="getClass"/>
                <message v-bind="result"></message>
            </div>
        </form>
    </div>
</template>

<script>

export default {
    data: function () {
        return {
            academic_id: '',
            success: true,
            url: route('entryChecking.store'),
            result: {
                message: 'ready',
                success: true,
                hide: true,
                errors: ['']
            },
        }
    },
    methods: {
        check_id() {
            if (0 === this.academic_id)
                return;
            let vue = this;
            let params = new FormData();
            params.append('academic_id', this.academic_id);
            vue.result.message = ''; //#todo more clever way to show if the value is the same
            axios.post(vue.url, params
            ).then(function (responseJson) {
                let json = responseJson['data'];
                vue.result.success = json['pass'];
                if (json['pass']) {
                    vue.result.message = json['passWith'];
                    vue.$emit('newEntry', json['passWith'] + 's');
                    vue.result.errors = [];
                } else
                    vue.result.message = "Request failed:";
                vue.result.errors = json;
            }).catch(function (errors) {
                vue.result.success = false;
                vue.result.errors = errors.response.data.errors;
                console.log(errors.response.data.errors)
                vue.result.message = "Request failed:";

            });
        }
    }
}
</script>

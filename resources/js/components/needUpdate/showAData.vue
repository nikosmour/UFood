<script>
export default {
	name : "showAData",
	props : {
		name :  {
			type :     String,
			required : true,
		},
		value : {
			required : true,
		},
	},
};
</script>

<template>
    <div class = "col-auto row">
        <label class = "col-auto m-auto"> {{ name }} </label>
        <label class = "col-auto m-auto"> {{ value }} </label>
    </div>

</template>

<style scoped>

</style>

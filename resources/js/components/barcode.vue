<template>
    <v-container>
        <v-card class = "pa-4 d-flex justify-center">
            <Barcode
                :value = "value"
                :format = "format"
                :display-value = "true"
                width = "1"
            ></Barcode>
        </v-card>
    </v-container>
</template>

<script setup>
import { defineAsyncComponent, onMounted, ref } from "vue";

const Barcode = defineAsyncComponent( () =>
	                                      import("@chenfengyuan/vue-barcode"),
);
// const Barcode = (await import("@chenfengyuan/vue-barcode")).default;// Import the correct barcode component
const props = defineProps( {
	                           value :        {
		                           type :     String,
		                           required : true,
	                           },
	                           format :       {
		                           type :    String,
		                           default : "CODE39", // Other formats: "EAN13", "UPC", etc.
	                           },
	                           width :        {
		                           type :    Number,
		                           default : 2,
	                           },
	                           height :       {
		                           type :    Number,
		                           default : 50,
	                           },
	                           displayValue : {
		                           type :    Boolean,
		                           default : true,
	                           },
                           } );

const barcodeCanvas = ref( null );
const barcodeWidth = ref( 2 ); // Dynamic width

const generateBarcode = async () => {
	if ( barcodeCanvas.value ) {
		// const JsBarcode = (await import("@chenfengyuan/vue-barcode")).default; // 🚀 Lazy load JsBarcode
		// JsBarcode( barcodeCanvas.value, props.value, {
		// 	format :       props.format,
		// 	width :        barcodeWidth.value,
		// 	height :       props.height,
		// 	displayValue : props.displayValue,
		// } );
	}
};

onMounted( generateBarcode );
</script>

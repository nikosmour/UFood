<template>
    <v-container max-width = "50rem">
        <v-row justify = "center">
            <v-col cols = "12">
                <v-stepper
                    v-model = "step"
                    :items = "[$t('personalInfo'), $t('documents.upload'), $t('pendingReview')]"
                    hide-actions
                >
                    <template v-slot:item.2>
                        <v-card :title = " $t('documents.upload')" class = "justify-content-around">
                            <MyCardApplicationFiles
                                :application = "application"
                            />
                            <v-card-actions class = "justify-space-between">
                                <v-btn
                                    :text = "$t('save')"
                                    color = "primary"
                                    variant = "elevated"
                                    @click = "saveApplication"
                                />
                                <v-btn
                                    :text = "$t('submit')"
                                    color = "primary"
                                    variant = "elevated"
                                    @click = "submitDocuments"
                                />
                            </v-card-actions>

                        </v-card>
                    </template>
                    <!--                    &lt;!&ndash; Step 1: Receiver Form &ndash;&gt;-->
                    <!--                    <template v-slot:item.1>-->
                    <!--                        <v-card :loading = "isLoading" :title = "$t('transaction.info')" flat>-->
                    <!--                            <v-form-->
                    <!--                                ref = "receiverForm" v-model = "isFormValid" validate-on = "invalid-input lazy"-->
                    <!--                                @submit.prevent = "confirmDataTransactions"-->
                    <!--                            >-->
                    <!--                                <v-row>-->
                    <!--                                    &lt;!&ndash; Receiver ID Field &ndash;&gt;-->
                    <!--                                    <v-col class = "mt-10" cols = "12" md = "8" offset-md = "2">-->
                    <!--                                        <v-text-field-->
                    <!--                                            ref = "receiverId"-->
                    <!--                                            v-model.number = "receiver.id"-->
                    <!--                                            :aria-label = "$t('receiver.value')"-->
                    <!--                                            :error-messages = "errors.receiver_id"-->
                    <!--                                            :label = "$t('receiver.value')"-->
                    <!--                                            :rules = "rules.receiver"-->
                    <!--                                            autofocus-->
                    <!--                                            density = "compact"-->
                    <!--                                            outlined-->
                    <!--                                            required-->
                    <!--                                            type = "number"-->
                    <!--                                            validate-on-blur-->
                    <!--                                            @input = "errors.receiver_id = null"-->
                    <!--                                        ></v-text-field>-->
                    <!--                                    </v-col>-->
                    <!--                                    -->
                    <!--                                    &lt;!&ndash; Meal Quantity Fields &ndash;&gt;-->
                    <!--                                    <v-col-->
                    <!--                                        v-for = "(meal, index) in mealPlanPeriods" :key = "'form.meals.' + index"-->
                    <!--                                        cols = "12" md = "8" offset-md = "2"-->
                    <!--                                    >-->
                    <!--                                        <v-text-field-->
                    <!--                                            :ref = "`meals-${meal}`"-->
                    <!--                                            v-model.number = "mealQuantities[meal]"-->
                    <!--                                            :disabled = "mealsDisable"-->
                    <!--                                            :error-messages = "errors[meal] || errors.meals"-->
                    <!--                                            :label = "$t('meals.' + meal.toLowerCase())"-->
                    <!--                                            :max = "couponOwner[meal] ?? null"-->
                    <!--                                            :rules = "rules['meals'][meal]"-->
                    <!--                                            density = "compact"-->
                    <!--                                            min = "0"-->
                    <!--                                            outlined-->
                    <!--                                            type = "number"-->
                    <!--                                            @input = "errors[meal] = errors['meals'] = null"-->
                    <!--                                        ></v-text-field>-->
                    <!--                                    </v-col>-->
                    <!--                                    -->
                    <!--                                    &lt;!&ndash; Next Button &ndash;&gt;-->
                    <!--                                    <v-col class = "d-flex justify-end" cols = "12">-->
                    <!--                                        <v-btn-->
                    <!--                                            :disabled = "isFormValid === false"-->
                    <!--                                            :text = "$t('next') "-->
                    <!--                                            color = "primary"-->
                    <!--                                            type = "submit"-->
                    <!--                                        />-->
                    <!--                                    </v-col>-->
                    <!--                                </v-row>-->
                    <!--                            </v-form>-->
                    <!--                        </v-card>-->
                    <!--                    </template>-->
                    <!--                    -->
                    <!--                    &lt;!&ndash; Step 2: Confirmation &ndash;&gt;-->
                    <!--                    <template v-slot:item.2>-->
                    <!--                        <v-card :loading = "isLoading" :title = "$t('transaction.info')">-->
                    <!--                            <v-card-text @keydown.enter = "handleSubmit">-->
                    <!--                                <my-show-list-item :list-items = "listItems" />-->
                    <!--                            </v-card-text>-->
                    <!--                            <v-card-actions class = "d-flex justify-space-between">-->
                    <!--                                <v-btn-->
                    <!--                                    :text = "$t('previous')"-->
                    <!--                                    color = "primary"-->
                    <!--                                    variant = "text"-->
                    <!--                                    @click = "step&#45;&#45;"-->
                    <!--                                />-->
                    <!--                                <v-btn-->
                    <!--                                    :text = "$t('confirm')"-->
                    <!--                                    color = "primary"-->
                    <!--                                    type = "submit"-->
                    <!--                                    variant = "elevated"-->
                    <!--                                    @click = "handleSubmit"-->
                    <!--                                />-->
                    <!--                            </v-card-actions>-->
                    <!--                        </v-card>-->
                    <!--                    </template>-->
                    <!--                    -->
                    <!--                    &lt;!&ndash; Step 3: Transaction Summary &ndash;&gt;-->
                    <!--                    <template v-slot:item.3>-->
                    <!--                        <v-card :title = "$t('transaction.info')">-->
                    <!--                            <v-card-text @keydown.enter = "handleSubmit">-->
                    <!--                                <my-show-list-item :list-items = "listItems" />-->
                    <!--                            </v-card-text>-->
                    <!--                            <v-card-actions class = "justify-center">-->
                    <!--                                <v-btn-->
                    <!--                                    :text = "$t('transaction.new')"-->
                    <!--                                    color = "primary"-->
                    <!--                                    variant = "elevated"-->
                    <!--                                    @click = "resetForm"-->
                    <!--                                />-->
                    <!--                            </v-card-actions>-->
                    <!--                        </v-card>-->
                    <!--                    </template>-->
                </v-stepper>
            </v-col>
        </v-row>
    </v-container>
</template>

<script>
// import MyShowListItem from "./MyShowListItem.vue";

import MyCardApplicationFiles from "../../components/MyCardApplicationFiles.vue";
import CardApplication from "../../models/CardApplication.js";

export default {
	name :       "CardApplication",
	components : { MyCardApplicationFiles },
	data() {
		return {
			application : null,
			step :  2,
		};
	},
	created() {
		const files = [
			...this.$enums.CardDocumentStatusEnum
			       .toArray()
			       .map( ( status, index ) => new Object(
				             {
					             file_name :   "name " + index,
					             description : "description " + index,
					             status :      status.value,
				             },
			             ),
			       ),
			...this.$enums.CardDocumentStatusEnum
			       .toArray()
			       .map( ( status, index ) => new Object(
				       {
					       file_name :   "name " + index,
					       description : "description " + index,
					       status :      status.value,
				       } ),
			       ),
		];
		this.application = new CardApplication(
			{
				id : 1,
				card_application_document : files,
			} );
	},
	methods : {
		saveApplication() {
			console.log( "saveApplication" );
		},
		submitDocuments() {
			console.log( "submitDocuments" );
		},
	},

};
</script>

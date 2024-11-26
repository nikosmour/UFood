<template>
    <v-card :loading = "isLoading">
        <!-- Card Application Section -->
        <v-row v-if = "!isLoading">
            <v-col cols = "12">
                <v-card>

                    <v-card-title>
                        <h4 class = "text-center">{{ title }}</h4>


                    </v-card-title>
                    <v-fab
                        :aria-label = "$t('add_file')"
                        absolute
                        class = "mr-3"
                        color = "primary"
                        icon = "mdi-file-plus"
                        location = "right"
                        offset
                        @click = "addFile"
                    />
                    <v-card-actions class = "justify-space-between">
                        <v-btn
                            v-if = "status === $enums.CardStatusEnum.TEMPORARY_SAVED"
                            :aria-label = "$t('save')"
                            color = "secondary"
                            @click = "changeToSave"
                        >
                            <v-icon start>mdi-content-save</v-icon>
                            {{ $t( "save" ) }}
                        </v-btn>
                        <v-btn
                            v-else-if = "applicationEdit"
                            :aria-label = "$t('edit')"
                            color = "secondary"
                            @click = "changeToEdit"
                        >
                            <v-icon start>mdi-pencil</v-icon>
                            {{ $t( "edit" ) }}
                        </v-btn>
                    </v-card-actions>

                    <!-- Form for Temporary Saved Status -->
                    <v-form
                        v-if = "status === $enums.CardStatusEnum.TEMPORARY_SAVED"
                        id = "card_application_form"
                        @submit.prevent = "submit_form"
                    >
                        <!-- Card Documents -->
                        <card-documents-show
                            ref = "CardDocuments"
                            :applicationEdit = "true"
                            :cardApplication = "cardApplication?.id"
                            @previewFile = "docLink = $event"
                        />
                        <!-- Comment Input -->
                        <v-text-field
                            v-model = "commentStudent"
                            :label = "$t('comment.enter')"
                            outlined
                        />
                        <!-- Submit Button -->
                        <v-btn color = "primary" type = "submit">
                            {{ $t( "submit" ) }}
                        </v-btn>
                    </v-form>

                    <!-- Read-Only Card Documents -->
                    <card-documents-show
                        v-else
                        :applicationEdit = "false"
                        :cardApplication = "cardApplication?.id"
                        @previewFile = "docLink = $event"
                    />
                </v-card>
                <message v-bind = "result" />
            </v-col>

            <!-- PDF Viewer -->
            <v-col v-if = "docLink">
                <v-sheet class = "pa-4" elevation = "1">
                    <object
                        :data = "docLink"
                        height = "500px"
                        type = "application/pdf"
                        width = "100%"
                    />
                </v-sheet>
            </v-col>
        </v-row>
    </v-card>
</template>

<script>
import { mapGetters } from "vuex";
import CardApplication from "@models/CardApplication.js";
import CardDocumentsShow from "../Components/CardDocumentsShow.vue";

export default {
	components : {
		CardDocumentsShow,
	},
	data() {
		return {
			// Initialize data properties
			result :          {
				message : this.$t( "test.message" ),
				success : true,
				hide :    false,
				errors :  [],
			},
			docLink :         "", // Initialize docLink
			cardApplication : null, // Initialize cardApplication
			commentStudent :  null, // Initialize commentStudent

		};
	},
	computed : {
		...mapGetters( "auth", {
			status : "cardStatus",
		} ),
		applicationEdit() {
			return [
				this.$enums.CardStatusEnum.INCOMPLETE,
				this.$enums.CardStatusEnum.SUBMITTED,
				this.$enums.CardStatusEnum.TEMPORARY_SAVED,
			].includes( this.status );
		},
		// Set title based on application mode
		title() {
			return document.title = this.$t( ( this.applicationEdit )
			                                 ? "card.edit"
			                                 : "card.show" );
		},
		isLoading() {
			return !this.cardApplication;
		},
	},
	methods :  {
		addFile() {
			return this.$refs.CardDocuments.openAddFileDialog();
		},
		// Method to listen for updates
		broadcasting() {
			if ( typeof this.$echo !== "undefined" )
				this.$echo
				    .private( `cardApplication.${ this.cardApplication.id }` )
				    .listen( "CardApplicationUpdated", ( e ) => {
					    this.cardApplication.expiration_date = new Date( e[ "expiration_date" ] );
					    this.cardApplication.card_last_update.status = this.$enums.CardStatusEnum.findByValue(
						    e[ "status" ],
					    );
				    } );
		},
		async changeToEdit() {
			return await this.update_form( this.$enums.CardStatusEnum.TEMPORARY_SAVED );
		},
		async changeToSave() {
			await this.$refs.CardDocuments.submitFiles();
			return await this.update_form( this.$enums.CardStatusEnum.TEMPORARY_SAVED );

		},
		// Method to fetch initial data
		startingData() {
			this.getApplication();
		},
		// Method to fetch card application data
		getApplication() {
			const url = this.route( "cardApplication.index" );
			console.log( "getApplication" );
			return this.$axios
			           .get( url )
			           .then( ( responseJson ) => {
				           const json = responseJson[ "data" ];
				           this.cardApplication = new CardApplication( json[ "cardApplication" ] );
				           this.broadcasting();
			           } )
			           .catch( errors => {
				           if ( errors.response?.status === 404 )
					           return this.$router.push( { name : "card.application.create" } );
				           throw errors;
			           } );

		},
		// Method to submit form
		async submit_form() {
			if ( !( await this.$refs.CardDocuments.submitFiles() ) ) {
				this.result.success = false;
				this.result.message = this.$t( "some_files_not_updated" );
				return;
			}
			return await this.update_form( this.$enums.CardStatusEnum.SUBMITTED );
		},
		async update_form( status ) {
			const url = this.route( "cardApplication.update", this.cardApplication );
			const params = new FormData();
			this.result.message = ""; //#todo more clever way to show if the value is the same
			if ( !this.applicationEdit ) {
				this.result.success = false;
				this.result.message = this.$t(
					"application_status_not_allow_submission",
				);
			}
			params.append( "_method", "PUT" );
			params.append( "status", status.value );
			if ( this.commentStudent ) {
				params.append( "comment", this.commentStudent );
			}
			console.log( "start axios to application for submission" );
			return this.$axios
			           .post( url, params )
			           .then( ( responseJson ) => {
				           const json = responseJson[ "data" ];
				           this.result.success = json[ "success" ];
				           this.result.message = json[ "success" ]
				                                 ? `${ this.$t( "application" ) } ${ this.$t( "changeFromTo", {
						           from1 : `status.${ this.cardApplication.card_last_update.status.key }`,
						           to1 :   `status.${ status.key }`,
					           } ) }`
				                                 : this.$t( json[ "message" ] );
				           this.result.errors = [];
			           } )
			           .catch( ( errors ) => {
				           this.result.success = false;
				           this.result.errors = errors.response.data.errors || errors;
				           this.result.message = this.$t( "request_failed_status_wont_change" );
			           } )
			           .finally( () => {
				           if ( this.result.success ) {
					           this.cardApplication.card_last_update.status = status;
				           }
			           } );
		},
	},
	created() {
		this.startingData();
	},
};

</script>

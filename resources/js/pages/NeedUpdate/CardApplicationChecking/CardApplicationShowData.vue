<template>
    <!--    <div v-if = "application" class = "row">-->

    <!--        <div class = " col-12  col-md-6 col-lg-5">-->

    <!--            <header class = "row">-->
    <!--                <h4 class = "text-left mt-3">{{ $t( "application" ) }}: {{ application.id }}</h4>-->
    <!--                <button-->
    <!--                    v-if = "isCheckingByUser" :aria-label = "$t('save')"-->
    <!--                    class = "btn btn-secondary" @click = "changeStatus($enums.CardStatusEnum.TEMPORARY_CHECKED)"-->
    <!--                >-->
    <!--                    <i aria-hidden = "true" class = "bi bi-save"></i>-->
    <!--                    <span class = "visually-hidden">{{ $t( "save" ) }}</span>-->
    <!--                </button>-->
    <!--                <button-->
    <!--                    v-else :aria-label = "$t('edit')" class = "btn btn-secondary"-->
    <!--                    @click = "changeStatus($enums.CardStatusEnum.CHECKING)"-->
    <!--                >-->
    <!--                    <i aria-hidden = "true" class = "bi bi-pencil"></i>-->
    <!--                    <span class = "visually-hidden">{{ $t( "edit" ) }}</span>-->
    <!--                </button>-->
    <!--            </header>-->
    <!--            <card-applicant-info :applicant = "application" />-->

    <!--            <div class = "">-->
    <!--                <h4>{{ $t( "applicationStatus" ) }}</h4>-->
    <!--                <div>-->
    <!--                    <label>{{ $t( "comment.latestFrom" ) }} {{-->
    <!--                            ( application.card_last_update.card_application_staff_id-->
    <!--                              ? $t( "staff" )-->
    <!--                              : $t( "applicant" ) ).toLowerCase()-->
    <!--                           }}:</label>-->
    <!--                    <p>{{ application.card_last_update.comment ?? $t( "comment.value", 0 ) }}</p>-->
    <!--                </div>-->
    <!--                <div>-->
    <!--                    <label for = "commentStaff">{{ $t( "comment.enter" ) }}</label>-->
    <!--                    <input-->
    <!--                        id = "commentStaff" v-model = "commentChecking"-->
    <!--                        :disabled = "!isCheckingByUser" class = "form-control mb-2" type = "text"-->
    <!--                    >-->
    <!--                    <label for = "expiration_date">{{ $t( "expiration date" ) }}</label>-->
    <!--                    <input-->
    <!--                        id = "expiration_date" v-model = "expirationDate" :disabled = "!isCheckingByUser"-->
    <!--                        class = "form-control mb-2" type = "date"-->
    <!--                    >-->
    <!--                </div>-->
    <!--                <div v-if = "isCheckingByUser" aria-label = "Status buttons" class = "btn-group mb-2" role = "group">-->
    <!--                    <button-->
    <!--                        :class = "{ active: application.card_last_update.status === $enums.CardStatusEnum.ACCEPTED }"-->
    <!--                        class = "btn btn-outline-primary" type = "button"-->
    <!--                        @click = "changeStatus($enums.CardStatusEnum.ACCEPTED)"-->
    <!--                    >-->
    <!--                        {{ $t( "status.accepted" ) }}-->
    <!--                    </button>-->
    <!--                    <button-->
    <!--                        :class = "{ active: application.card_last_update.status === $enums.CardStatusEnum.REJECTED }"-->
    <!--                        class = "btn btn-outline-secondary" type = "button"-->
    <!--                        @click = "changeStatus($enums.CardStatusEnum.REJECTED)"-->
    <!--                    >-->
    <!--                        {{ $t( "status.rejected" ) }}-->
    <!--                    </button>-->
    <!--                    <button-->
    <!--                        :class = "{ active: application.card_last_update.status === $enums.CardStatusEnum.INCOMPLETE }"-->
    <!--                        class = "btn btn-outline-warning" type = "button"-->
    <!--                        @click = "changeStatus($enums.CardStatusEnum.INCOMPLETE)"-->
    <!--                    >-->
    <!--                        {{ $t( "status.incomplete" ) }}-->
    <!--                    </button>-->
    <!--                </div>-->
    <!--                <message v-bind = "result"></message>-->
    <!--            </div>-->
    <!--        </div>-->

    <!--        <div class = "col-12 col-md-6 col-lg-7">-->
    <!--            <div class = "row">-->
    <!--                <h5 class = "mt-3 col-auto">{{ $t( "file", 2 ) }}</h5>-->
    <!--                <div class = "mb-3 col-auto">-->
    <!--                    <select v-model = "selectFile" :aria-label = "$t('select_file')" class = "form-select">-->
    <!--                        <option v-for = "file in files" :key = "file.id" :value = "file">{{ $t( "file" ) }}: {{-->
    <!--                                file.id-->
    <!--                                                                                         }}-->
    <!--                        </option>-->
    <!--                    </select>-->
    <!--                </div>-->
    <!--                <div v-if = "isCheckingByUser && selectFile" class = "mb-3 col-auto">-->
    <!--                    <select-->
    <!--                        v-model = "selectFile.status" :aria-label = "$t('file_status')" class = "form-select"-->
    <!--                        @change = "updateDocumentStatus(selectFile)"-->
    <!--                    >-->
    <!--                        <option disabled value = "">{{ $t( "pleaseSelect" ) }}</option>-->
    <!--                        <option-->
    <!--                            v-for = "(value, status) in $enums.CardDocumentStatusEnum" :key = "status" :value = "value"-->
    <!--                        >-->
    <!--                            {{ $t( "status." + status.toLowerCase() ) }}-->
    <!--                        </option>-->
    <!--                    </select>-->
    <!--                </div>-->
    <!--                <message v-bind = "resultFile"></message>-->
    <!--            </div>-->
    <!--            <object-->
    <!--                v-if = "selectFile" :data = "selectedFileUrl" class = "pdf-object" type = "application/pdf"-->
    <!--            ></object>-->
    <!--        </div>-->
    <!--    </div>-->
    <!--    <ApplicationPreview :application="newApplication" v-if="newApplication" :applicant="newApplication.academic"/>-->

    <v-card v-if = "newApplication" :title = "$t('application')+': ' +newApplication.id">
        <v-card-text>
            <v-expansion-panels v-if = "newApplication" v-model = "panel" class = "mb-5" multiple>
                <v-expansion-panel :title = "$t('model_data.applicant_info')">
                    <v-expansion-panel-text>
                        <CardApplicantInfo :user = "newApplication.academic" />
                    </v-expansion-panel-text>
                </v-expansion-panel>

                <v-expansion-panel :title = "$t('document',2)">
                    <v-expansion-panel-text>
                        <MyCardApplicationFiles
                            :application = "newApplication"
                            :isAcademic = "false"
                            :loadings = "[]"
                        />
                    </v-expansion-panel-text>
                </v-expansion-panel>
                <v-expansion-panel :title = "$t('card_info')">
                    current status : {{ currentStatus.value }}
                    <v-textarea :label = "$t('comment')" :model-value = "'jhkkt'" auto-grow rows = "2">
                    </v-textarea>
                </v-expansion-panel>
            </v-expansion-panels>
        </v-card-text>
        <v-card-actions
            v-if = "true ||isCheckingByUser" aria-label = "Status buttons" class = "justify-space-between"
            role = "group"
        >
            <v-btn
                color = "success"
                variant = "elevated"
                        @click = "changeStatus($enums.CardStatusEnum.ACCEPTED)"
                    >
                        {{ $t( "status.accepted" ) }}
            </v-btn>
            <v-btn
                color = "warning"
                variant = "elevated"
                @click = "changeStatus($enums.CardStatusEnum.INCOMPLETE)"
            >
                {{ $t( "status.incomplete" ) }}
            </v-btn>
            <v-btn
                color = "error"
                variant = "elevated"
                        @click = "changeStatus($enums.CardStatusEnum.REJECTED)"
                    >
                        {{ $t( "status.rejected" ) }}
            </v-btn>
        </v-card-actions>
        <v-dialog v-model = "showDecisionDialog" max-width = "50em">
            <v-card>
                <v-card-title>
                    {{ $t( "updateStatus" ) + $t( "status." + "newApplication.card_last_update.status.value" ) }}
                </v-card-title>

                <v-card-text>
                    <v-form ref = "fileForm">
                        <!-- File Description -->
                        <v-textarea v-model = "commentChecking" :label = "$t('comment.enter')" auto-grow rows = "2">
                        </v-textarea>
                        <v-text-field v-model = "expirationDate" type = "date" />

                        <!-- File Upload (only for new files) -->
                        <v-date-input
                            v-model = "expirationDate"
                            :label = "$t('expire_date')"
                            outlined
                            required
                        />
                    </v-form>
                </v-card-text>

                <v-card-actions class = "justify-end">
                    <v-btn color = "secondary" @click = "restoreStatus">
                        {{ $t( "cancel" ) }}
                    </v-btn>
                    <v-btn
                        color = "primary"
                        @click = "updateApplicationStatus"
                    >
                        {{ $t( "confirm" ) }}
                    </v-btn>
                </v-card-actions>
            </v-card>
        </v-dialog>
    </v-card>
</template>

<script>
import CardApplicantInfo from "@components/needUpdate/cardApplicantInfo.vue";
import { mapGetters } from "vuex";
import Message from "@pages/NeedUpdate/CardApplicationChecking/Message.vue";
import ApplicationPreview from "@pages/Card/ApplicationPreview.vue";
import CardApplication from "@models/CardApplication.js";
import MyCardApplicationFiles from "@components/MyCardApplicationFiles.vue";

export default {
	components : {
		MyCardApplicationFiles,
		ApplicationPreview,
		Message,
		CardApplicantInfo,
	},
	props :      {
		application : Object,
	},
	data() {
		return {
			panel :              [ 0 ],
			showDecisionDialog : false,
			newApplication :     null,
			resultFile :        {
				message : "",
				success : null,
				hide :    true,
				errors :  [],
			},
			currentStatus :     null,
			currentFileStatus : null,
			selectFile :        null,
			files :             [],
			commentChecking :   null,
			expirationDate :    null,
			result :            {
				message : "",
				success : true,
				hide :    false,
				errors :  [],
			},
		};
	},
	methods :  {
		startingData() {
			this.currentStatus = this.newApplication.card_last_update.status;
			this.files = this.newApplication.card_application_document;
			this.expirationDate = this.newApplication.expiration_date;
		},
		async updateDocumentStatus( file ) {
			let params = new FormData();
			let url = this.route( "document.update", { "document" : file.id } );
			this.resultFile.message = "";
			params.append( "_method", "PUT" );
			params.append( "status", file.status );
			try {
				const response = await this.$axios.post( url, params );
				let json = response.data;
				this.resultFile.success = json.success;
				this.resultFile.message = json.message;
				this.resultFile.errors = [];
				return json.success;
			} catch ( errors ) {
				this.resultFile.success = false;
				this.resultFile.errors = errors.response.data.errors;
				this.resultFile.message = this.$t( "request_failed" );
				return false;
			}
		},
		changeStatus( status ) {
			console.info( "changeStatus", this.newApplication );
			this.newApplication.card_last_update.status = status;
			this.showDecisionDialog = true;
		},
		restoreStatus() {
			this.newApplication.card_last_update.status = this.currentStatus;
		},
		updateApplicationStatus() {
			const application = this.newApplication;
			let params = new FormData();
			params.append( "status", application.card_last_update.status );
			params.append( "card_application_id", application.id );
			if ( this.expirationDate && application.card_last_update.status === this.$enums.CardStatusEnum.ACCEPTED ) {
				params.append( "expiration_date", this.expirationDate );
			}
			if ( this.commentChecking ) {
				params.append( "card_application_staff_comment", this.commentChecking );
			}
			this.$axios.post(
				this.route( "cardApplication.checking.store",
				            { "category" : application.card_last_update.status.value } ),
				params )
			    .then( response => {
				    this.showDecisionDialog = false;
				    this.$notify( {
					                  error : "success update",
					                  color : "success",
				                  } );
			    } )
			    .catch( errors => {
				    console.info( "updateApplicationStatus catch", errors );
				    this.$displayError( { error : errors } );
				    this.result.message = this.$t( "request_failed" );
				    this.result.errors = errors.response.data.errors;
				    this.result.success = false;
				    application.card_last_update.status = this.currentStatus;
			    } );
		},
	},
	computed : {


		selectedFileUrl() {
			return this.route( "document.show", { "document" : this.selectFile?.id } );
		},
		...mapGetters( "auth", [
			"currentUser",
		] ),
		isCheckingByUser() {
			return this.newApplication?.card_last_update.status === this.$enums.CardStatusEnum.CHECKING
			       && this.newApplication?.card_last_update.card_application_staff_id === this.currentUser.id;
		},
	},
	watch :    {
		application( newValue ) {
			console.info( "application", newValue );
			this.newApplication = ( newValue )
			                      ? new CardApplication( newValue )
			                      : null;
			if ( newValue ) this.startingData();
			this.selectFile = null;
		},
		async selectFile( newValue, oldValue ) {
			if ( oldValue && oldValue.status !== this.currentFileStatus ) {
				if ( !await this.updateDocumentStatus( oldValue ) ) {
					oldValue.status = this.currentFileStatus;
				}
			}
			this.currentFileStatus = newValue
			                         ? newValue.status
			                         : null;
		},
	},
	created() {
		console.info( "created", this.application );
		this.newApplication = ( this.application )
		                      ? new CardApplication( this.application )
		                      : null;

		if ( this.application ) this.startingData();
	},
};
</script>

<style scoped>
.pdf-object {
    height: 500px;
    width: 100%;
    border: 1px solid #ccc;
}
</style>

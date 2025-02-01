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
    <!--    <ApplicationPreview :application="application" v-if="application" :applicant="application.academic"/>-->

    <v-card v-if = "application" :loading = "isLoading">
        <v-card-title class = "d-flex justify-space-between align-center">
            <span>{{ $t( "application" ) + ": " + application.id }} {{
                    $t( "status." + application.card_last_update.status.key.toLowerCase() ) }}</span>
            <v-btn-group>

                <!-- Save Button (Icon) -->
                <v-btn
                    :aria-label = "$t('save')" icon v-if = "isCheckingByUser"
                    @click = "changeStatus($enums.CardStatusEnum.TEMPORARY_CHECKED)"
                >
                    <v-icon>mdi-content-save-all</v-icon>
                </v-btn>

                <!-- Edit Button (Icon) -->
                <v-btn :aria-label = "$t('edit')" icon @click = "requestEdit" v-if = "!cantCheckingByUser ">
                    <v-icon :icon = "'mdi-pencil'"></v-icon>
                </v-btn>
            </v-btn-group>
        </v-card-title>
        <v-card-text>
            <v-expansion-panels v-if = "application" v-model = "panel" class = "mb-5" multiple>
                <v-expansion-panel :title = "$t('model_data.applicant_info')">
                    <v-expansion-panel-text>
                        <CardApplicantInfo :user = "application.academic" />
                    </v-expansion-panel-text>
                </v-expansion-panel>

                <v-expansion-panel :title = "$t('document',2)">
                    <v-expansion-panel-text>
                        <MyCardApplicationFiles
                            :application = "application"
                            :isAcademic = "false"
                            :is-editing = "isCheckingByUser"
                            :loadings = "loading"
                            @updateStatus = "updateDocumentStatus"
                        />
                    </v-expansion-panel-text>
                </v-expansion-panel>
            </v-expansion-panels>
            <v-textarea
                :label = "$t('comment.student')" :model-value = "application.card_applicant_update_latest?.comment"
                auto-grow readonly rows = "2"
            >
            </v-textarea>
            {{ $t( "model_data.expiration_date" ) }} : {{ expirationDate }}
        </v-card-text>
        <v-card-actions
            v-if = "isCheckingByUser" aria-label = "Status buttons" class = "justify-space-between"
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
            <v-card :loading = "isLoading">
                <v-card-title>
                    {{ $t( "status.update", {
                    newStatus : $t( "status." + application.card_last_update.status.key.toLowerCase() ),
                               oldStatus : $t( "status." + currentStatus.key.toLowerCase() ),
                           },
                ) }}
                </v-card-title>

                <v-card-text>
                    <v-form ref = "fileForm">
                        <!-- File Description -->
                        <v-textarea
                            v-if = "application.card_last_update.status!==$enums.CardStatusEnum.ACCEPTED "
                            v-model = "commentChecking"
                            :label = "$t('comment.enter')+'*'"
                            :error-messages = "errors['card_application_staff_comment']"
                            v-on:change = "errors['card_application_staff_comment']=[]"
                            auto-grow
                            rows = "2"
                        />
                        <v-text-field
                            v-else
                            v-model = "expirationDate"
                            :error-messages = "errors['expiration_date']"
                            :label = "$t('model_data.expiration_date')"
                            v-on:change = "errors['expiration_date']=[]"
                            type = "date"
                        />

                        <!--                        <v-date-input-->
                        <!--                            v-model = "expirationDate"-->
                        <!--                            :label = "$t('expire_date')"-->
                        <!--                            outlined-->
                        <!--                            required-->
                        <!--                        />-->
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
import { mapGetters, mapMutations } from "vuex";
import ApplicationPreview from "@pages/Card/ApplicationPreview.vue";
import MyCardApplicationFiles from "@components/MyCardApplicationFiles.vue";

export default {
	components : {
		MyCardApplicationFiles,
		ApplicationPreview,
		CardApplicantInfo,
	},
	props :      {
		application : Object,
	},
	data() {
		return {
			panel :              [ 0 ],
			showDecisionDialog : false,
			loading :        [],
			// resultFile :        {
			// 	message : "",
			// 	success : null,
			// 	hide :    true,
			// 	errors :  [],
			// },
			currentStatus :     null,
			currentFileStatus : null,
			selectFile :        null,
			files :             [],
			commentChecking :   null,
			expirationDate :    null,
			// result :            {
			// 	message : "",
			// 	success : true,
			// 	hide :    false,
			errors : {
				"expiration_date" :                [],
				"card_application_staff_comment" : [],
			},
			// },
		};
	},
	methods :  {
		startingData() {
			this.currentStatus = this.application.card_last_update.status;
			this.files = this.application.card_application_document;
			this.expirationDate = this.application.expiration_date.toLocaleDateString( "en-ca" );//toISOString().split( "T" )[ 0 ];

		},
		async updateDocumentStatus( file ) {
			let params = new FormData();
			let url = this.route( "document.update", { "document" : file.id } );
			// this.resultFile.message = "";
			params.append( "_method", "PUT" );
			params.append( "status", file.status.value );
			this.loading.push( true );
			try {
				const response = await this.$axios.post( url, params );
				let json = response.data;
				this.$notify( {
					              error : $t( json.message ),
					              color : "success",
				              } );
			} catch ( errors ) {
				// this.resultFile.success = false;
				// this.resultFile.errors = errors.response.data.errors;
				// this.resultFile.message = this.$t( "request_failed" );
				return false;
			} finally {
				this.loading.pop();
			}
		},
		changeStatus( status ) {
			console.info( "changeStatus", this.application );
			this.application.card_last_update.status = status;
			this.showDecisionDialog = true;
		},
		async requestEdit() {
			try {
				this.loading.push( true );
				this.application.card_last_update = await this.application.requestToEdit( false );
				console.info( "response", this.application.card_last_update );
				this.currentStatus = this.application.card_last_update.status;
			} catch ( error ) {
				// application.card_last_update.status = this.currentStatus;
				if ( error.response?.status === 422 ) {
					this.errors = error.response.errors;
					this.$displayError( error.message );
				} else {
					throw error;
				}
			} finally {
				this.loading.pop();
			}
		},
		restoreStatus() {
			this.application.card_last_update.status = this.currentStatus;
			this.showDecisionDialog = false;
		},
		async updateApplicationStatus() {
			const application = this.application;
			let params = new FormData();
			params.append( "status", application.card_last_update.status.value );
			params.append( "card_application_id", application.id );
			if ( application.card_last_update.status === this.$enums.CardStatusEnum.ACCEPTED ) {
				params.append( "expiration_date", this.expirationDate );
			} else {
				params.append( "card_application_staff_comment", this.commentChecking );
			}
			this.loading.push( true );
			try {
				await this.$axios.post(
					this.route( "cardApplication.checking.store",
					            { "category" : application.card_last_update.status.value } ),
					params );
				this.showDecisionDialog = false;
				this.$notify( {
					              error : this.$t( "application-updated" ),
					              color : "success",
				              } );
				this.currentStatus = application.card_last_update.status;
			} catch ( errors ) {
				if ( errors.response?.status === 422 )
					this.errors = errors.response.data.errors;
			} finally {
				this.loading.pop();
			}
		},
		...mapMutations( "files", [ "setPreviewUrl" ] ),
	},
	computed : {
		isLoading() {
			return !!this.loading.length;
		},
		...mapGetters( "auth", [
			"currentUser",
		] ),
		isCheckingByUser() {
			return this.application?.card_last_update.status === this.$enums.CardStatusEnum.CHECKING
			       && this.application?.card_last_update.card_application_staff_id === this.currentUser.id;
		},
		cantCheckingByUser() {
			return [
				this.$enums.CardStatusEnum.CHECKING,
				this.$enums.CardStatusEnum.TEMPORARY_SAVED,
			].includes( this.application?.card_last_update.status );
		},
	},
	watch :    {
		application( newValue ) {
			if ( newValue ) this.startingData();
			this.selectFile = null;
			this.setPreviewUrl( null );
			this.panel = [ 0 ];
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
		if ( this.application ) this.startingData();
	},
	unmounted() {
		this.setPreviewUrl( null );
	},
};
</script>

<template>
    <v-card v-if = "applicationEdit">

        <!-- Add/Edit File Dialog -->
        <v-dialog v-model = "showFileDialog" max-width = "600">
            <v-card>
                <v-card-title>
                    {{ isEditingFile
                       ? $t( "edit_file" )
                       : $t( "add_file" ) }}
                </v-card-title>
                <v-card-text>
                    <v-form ref = "fileForm" v-model = "isFormValid">
                        <!-- File Description -->
                        <v-text-field
                            v-model = "fileInput.description"
                            :label = "$t('description')"
                            :rules = "[value => !!value || $t('description_required')]"
                            outlined
                            required
                        />

                        <!-- File Upload -->
                        <v-file-input
                            v-if = "!isEditingFile"
                            v-model = "fileInput.file"
                            :label = "$t('select_file')"
                            :rules = "[value => !!value || $t('file_required')]"
                            accept = "application/pdf"
                            outlined
                            required
                        />
                    </v-form>
                </v-card-text>
                <v-card-actions class = "justify-end">
                    <v-btn color = "secondary" @click = "closeFileDialog">
                        {{ $t( "cancel" ) }}
                    </v-btn>
                    <v-btn
                        :disabled = "!isFormValid || (!isEditingFile && !fileInput.file)"
                        color = "primary"
                        @click = "handleFileSubmit"
                    >
                        {{ $t( "confirm" ) }}
                    </v-btn>
                </v-card-actions>
            </v-card>
        </v-dialog>
        <!-- File List -->
        <v-container>

            <!-- Floating Action Button (FAB) -->
            <!--            <v-fab
                            :aria-label = "$t('add_file')"
                            color = "primary"
                            style="z-index: 999;"
                            absolute
                            offset
                            icon = "mdi-file-plus"
                            location = "top right"
                            @click = "openAddFileDialog"
                        />-->
            <v-row v-for = "(file, index) in files" :key = "index" class = "mt-3">
                <v-col>
                    <v-text-field
                        v-model = "file.description"
                        :disabled = "!canEditDocument[index]"
                        :label = "$t('description')"
                        outlined
                    />
                    <message v-bind = "file.result" />
                </v-col>
                <v-col cols = "auto">
                    <v-btn
                        color = "secondary"
                        outlined
                        @click = "previewFile($event, index)"
                    >
                        {{ $t( "preview" ) }}
                    </v-btn>
                    <v-btn
                        v-if = "canEditDocument[index]"
                        color = "red"
                        outlined
                        @click = "markForDeletion(index)"
                    >
                        {{ $t( "status.delete" ) }} {{ file.id }}
                    </v-btn>
                </v-col>
            </v-row>
        </v-container>
    </v-card>

    <div v-else>
        <div
            v-for = "(file, index) in files"
            :key = "index"
            class = "row justify-content-between ms-auto me-auto mt-4"
        >
            <div class = "col-auto">
                <label>{{ file.description }}</label>
                <message v-bind = "file.result" />
            </div>
            <button
                class = "btn btn-secondary col-auto"
                @click = "previewFile($event, index)"
            >
                {{ $t( "preview" ) }} {{ file.id }}
            </button>
        </div>
    </div>
</template>
<script>
export default {
	name :     "CardDocumentsShow",
	emits :    [
		"previewFile",
	],
	props : {
		applicationEdit : Boolean,
		cardApplication : Number,
	},
	data() {
		return {
			docFiles :         [], // Original list of document files
			files :            [], // Array to store files and their metadata
			docLink :          "", // File preview link
			showFileDialog :   false, // Dialog visibility for adding/editing files
			isEditingFile :    false, // True if editing an existing file
			editingFileIndex : null, // Index of file being edited
			fileInput :        {
				file :        null,
				description : "",
			},
			isFormValid :      false, // Validity of the form
		};
	},
	computed : {
		canEditDocument : function () {
			return this.files.map( ( file, index ) => {
				return [
					null,
					"to delete",
					this.$enums.CardDocumentStatusEnum.INCOMPLETE,
					this.$enums.CardDocumentStatusEnum.SUBMITTED,
				].includes( file.status );
			} );

		},
	},
	methods :  {


		/**
		 * Fetches document data from the backend.
		 */
		getDocuments() {
			const url = this.route( "document.index", { cardApplication : this.cardApplication } );
			this.$axios.get( url )
			    .then( ( response ) => {
				    const documents = response.data.documents;
				    this.docFiles = documents;
				    documents.forEach( ( file ) => {
					    this.addFileUpload( null, file.status, file.description, file.id,
					                        this.route( "document.show", { document : file.id } ) );
				    } );
			    } )
			    .catch( ( errors ) => {
				    this.result.success = false;
				    this.result.message = this.$t( "retrieving_application_files_failed" );
				    throw errors;
			    } );
		},

		/**
		 * Opens the dialog to add a new file.
		 */
		openAddFileDialog() {
			this.isEditingFile = false;
			this.fileInput = {
				file :        null,
				description : "",
			};
			this.showFileDialog = true;
		},

		/**
		 * Opens the dialog to edit an existing file.
		 * @param {Number} index - Index of the file to edit
		 */
		openEditFileDialog( index ) {
			this.isEditingFile = true;
			this.editingFileIndex = index;
			const file = this.files[ index ];
			this.fileInput = {
				file :        null, // No need to re-upload the file for editing
				description : file.description,
			};
			this.showFileDialog = true;
		},

		/**
		 * Closes the file dialog.
		 */
		closeFileDialog() {
			this.showFileDialog = false;
			this.fileInput = {
				file :        null,
				description : "",
			};
		},

		/**
		 * Handles submission of the file dialog form.
		 */
		handleFileSubmit() {
			if ( !this.fileInput.description ) {
				alert( this.$t( "please_enter_description" ) );
				return;
			}

			if ( this.isEditingFile ) {
				// Update an existing file
				const file = this.files[ this.editingFileIndex ];
				file.description = this.fileInput.description;
				this.$set( this.files, this.editingFileIndex, file );
			} else {
				// Add a new file
				this.addFileUpload(
					this.fileInput.file,
					null,
					this.fileInput.description,
				);
			}
			this.closeFileDialog();
		},

		/**
		 * Marks a file for deletion or removes it if unsaved.
		 * @param {Number} index - Index of the file to mark for deletion
		 */
		markForDeletion( index ) {
			const file = this.files[ index ];
			if ( file.id !== 0 ) {
				file.status = "to delete";
			} else {
				this.files.splice( index, 1 );
			}
		},

		/**
		 * Adds a new file or updates an existing one in the files array.
		 * @param {File|null} file - The file object
		 * @param {String|null} status - The status of the file
		 * @param {String} description - Description of the file
		 * @param {Number} id - ID of the file (default 0 for new files)
		 * @param {String} link - Link to the file (default empty)
		 * @param {String} message - Result message
		 * @param {Boolean|null} success - Success status
		 */
		addFileUpload( file = null, status = null, description = "", id = 0, link = "", message = "", success = null ) {
			this.files.push( {
				                 file,
				                 id,
				                 description,
				                 status,
				                 link,
				                 result : {
					                 message : message || this.$t( "status.category." + status ),
					                 success,
					                 hide :    false,
					                 errors :  [],
				                 },
			                 } );
		},

		/**
		 * Previews a selected file.
		 * @param {Event} event - The event object
		 * @param {Number} index - Index of the file to preview
		 */
		previewFile( event, index ) {
			const file = this.files[ index ];
			if ( file.link ) {
				this.docLink = file.link;
				return;
			}
			if ( file.file ) {
				const reader = new FileReader();
				reader.onload = ( e ) => {
					this.docLink = file.link = e.target.result;
				};
				reader.readAsDataURL( file.file );
			}
		},

		/**
		 * Submits all files to the server.
		 * @returns boolean - If successfully upload all the files
		 */
		async submitFiles() {
			const promises = this.files.map( ( file, index ) => this.prepareUploadFile( file, index ) );
			const results = await Promise.all( promises );
			return !results.includes( false );
		},

		/**
		 * @param {string} url
		 * @param {FormData} params
		 * @param {Object} file
		 * @param {Number} index
		 * @returns Promise<axios.AxiosResponse<any>>
		 */
		getPromise( url, params, file, index ) {
			return this.$axios.post( url, params )
			           .then( responseJson => {
				           let json = responseJson[ "data" ];
				           file.id = json[ "id" ];
				           file.result.success = json[ "success" ];
				           file.result.message = this.$t( json[ "message" ] );
				           file.result.errors = [];
			           } )
			           .catch( errors => {
				           console.log( errors );
				           if ( errors.response?.status === 422 )
					           console.error( errors.response?.data?.errors );
				           file.result.message = `${ this.$t( "request_failed" ) } : `;
				           file.result.message +=
					           this.$t( "status.category." + file.status ) + this.$t( "uploadedNot" );
				           file.result.success = false;
			           } )
			           .finally( () => {
				           file.link = "";
				           if ( file.result.success ) {
					           this.docFiles[ index ].status = file.status = ( "to delete" === file.status )
					                                                         ? "deleted"
					                                                         : "submitted";
					           this.docFiles[ index ].description = file.description;
					           this.docFiles[ index ].id = file.id;
				           }
				           file.result.message +=
					           this.$t( "status.category." + file.status ) + ( !file.result.success )
					           ? ""
					           : " " + this.$t( "uploadedNot" );
				           return file.result.success;
			           } );
		},
		/**
		 * Uploads a single file to the server.
		 * @param {Object} file - The file object
		 * @param {Number} index - Index of the file in the array
		 * @returns boolean
		 */
		async prepareUploadFile( file, index ) {
			console.log( "file", file.file );
			const params = new FormData();
			let url;
			file.result.message = ""; //#todo more clever way to show if the value is the same
			//is it need to delete the file;
			// Check if file can be edited
			if ( !this.canEditDocument[ index ] || !this.applicationEdit ) {
				file.result.message = this.$t( "file_cant_edited" );
				return file.result.success = false;
			}
			if ( this.$enums.CardDocumentStatusEnum.INCOMPLETE === file.status ) {
				file.result.success = false;
				throw ( this.$t( "incomplete file" ) );
			}
			if ( file.file === null ) {
				file.result.message = this.$t( "no_file" );
				return file.result.success = true;
			}
			if ( "to delete" === file.status ) {
				url = this.route( "document.destroy", { "document" : file.id } );
				params.append( "_method", "DELETE" );
			} else if ( 0 === file.id ) { // Submit a new file
					params.append( "file", file.file );
					params.append( "description", file.description );
					url = this.route( "document.store", { "cardApplication" : this.cardApplication } );
			} else if ( file.description !== this.docFiles[ index ].description ) { //update existing file description
				url = this.route( "document.update", { "document" : file.id } );
				params.append( "description", file.description );
				params.append( "_method", "PUT" );
			} else {
				file.result.message = this.$t( "file_submitted.already" );
				return file.result.success = true;
			}
			// Make axios request to upload file
			return await this.getPromise( url, params, file, index );
		},
	},
	watch :    {
		cardApplication : "getDocuments",
		docLink( newLink ) {
			this.$emit( "previewFile", newLink );
		},
	},
	created() {
		if ( this.cardApplication ) this.getDocuments();
	},
};
</script>


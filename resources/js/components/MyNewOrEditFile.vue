<script>
import CardApplicationDocument from "@models/CardApplicationDocument.js";

export default {
	name : "MyNewOrEditFile",
	props : {
		fileOptions : {
			type :     Object,
			required : true,
		},
	},

	/**
	 * Emits:
	 * - `addFile`: Triggered when a new file is added.
	 */
	emits : [ "addFile" ],

	data() {
		return {
			/**
			 * Indicates the visibility of the dialog for adding or editing files.
			 * @type {boolean}
			 */
			showFileDialog : false,

			/**
			 * Indicates if the dialog is in editing mode.
			 * @type {boolean}
			 */
			isEditingFile : false,

			/**
			 * Holds the current file input data.
			 * @type {Object}
			 * @property {File|null} file - The file to be uploaded.
			 * @property {string} description - Description of the file.
			 */
			fileInput : {
				file : [],
				description : "",
			},

			/**
			 * Indicates the validity of the form.
			 * @type {boolean}
			 */
			isFormValid : false,

			/**
			 * The file being edited (if in editing mode).
			 * @type {CardApplicationDocument|null}
			 */
			file : null,
		};
	},

	computed : {
		/**
		 * Validation rules for the form inputs.
		 * @return {Object} An object containing validation rules for `description` and `file`.
		 * @property {Array<function>} description - Validation rules for the file description.
		 * @property {Array<function>} file - Validation rules for the file input.
		 */
		rules() {
			return {
				description : [
					( value ) =>
						!!value ||
						this.$t( "validation.required", {
							attribute : this.$t( "description" ),
						} ),
					( value ) =>
						!value || value.length < 28 ||
						this.$t( "validation.max.string", {
							attribute : this.$t( "description" ),
							max :       27,
						} ),

				],
				file : [
					( files ) => files.length || this.$t( "validation.file" ),
					( files ) => !files || !files.some( file => file.size > 2_097_152 ) ||
					             this.$t( "validation.lt.file", {
						             attribute : this.$t( "file.value" ),
						             value :     2_048,
					             } ),
				],
			};
		},

	},

	methods : {
		/**
		 * Opens the dialog to add a new file.
		 * Resets the `fileInput` state and switches to adding mode.
		 */
		openAddFileDialog() {
			this.openFileDialog( null, false );
		},

		/**
		 * Reopens the dialog to add a new file for the case something went wrong.
		 * Populates the `fileInput` with the file's description and file data.
		 * @param {CardApplicationDocument} file - The file to be edited.
		 * @param {string} file.description - The description of the file.
		 */
		reOpenAddFileDialog( file ) {
			this.openFileDialog( file, false );
		},

		/**
		 * Opens the dialog to edit an existing file.
		 * Populates the `fileInput` with the file's description.
		 *
		 * @param {CardApplicationDocument} file - The file to be edited.
		 * @param {string} file.description - The description of the file.
		 */
		openEditFileDialog( file ) {
			this.openFileDialog( file, true );
		},

		/**
		 * Opens the dialog to add a new or edit an existing file.
		 * Populates the `fileInput` with the appropriate data.
		 *
		 * @param {CardApplicationDocument} file - The file to be edited.
		 * @param {boolean} isEditingFile -Is edit mode ?
		 * @param {string} file.description - The description of the file.
		 */
		openFileDialog( file, isEditingFile ) {
			this.isEditingFile = isEditingFile;
			this.fileInput = {
				file :        file?.file ?? null,
				description : file?.description ?? "",
			};
			this.file = file;
			this.showFileDialog = true;
		},

		/**
		 * Closes the file dialog and resets the input state.
		 */
		closeFileDialog() {
			this.showFileDialog = false;
			this.fileInput = {
				file : null,
				description : "",
			};
			this.file = null;
		},

		/**
		 * Handles submission of the file form.
		 * Validates the form, updates an existing file's description if editing,
		 * or emits a new file object for adding.
		 */
		handleFileSubmit() {
			if ( !this.fileInput.description ) {
				alert( this.$t( "please_enter_description" ) );
				return;
			}

			if ( this.isEditingFile ) {
				// Update the description of the existing file
				this.file.description = this.fileInput.description;
			} else {
				// Create a new file and emit it
				const newFile = CardApplicationDocument.newDocument( this.fileInput );
				this.$emit( "addFile", newFile );
			}

			this.closeFileDialog();
		},
	},
};
</script>

<template>
    <v-dialog v-model = "showFileDialog" max-width = "600">
        <v-card>
            <v-card-title>
                {{ isEditingFile
                   ? $t( "file.edit" )
                   : $t( "file.add" ) }}
            </v-card-title>

            <v-card-text>
                <v-form ref = "fileForm" v-model = "isFormValid">
                    <!-- File Description -->
                    <v-autocomplete
                        v-model = "fileInput.description"
                        :items = "fileOptions"
                        :label = "$t('description')"
                        :rules = "rules.description"
                        outlined
                        required
                    />

                    <!-- File Upload (only for new files) -->
                    <v-file-input
                        v-if = "!isEditingFile"
                        v-model = "fileInput.file"
                        :label = "$t('file.select')"
                        :rules = "rules.file"
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
                    {{ $t( "confirmation" ) }}
                </v-btn>
            </v-card-actions>
        </v-card>
    </v-dialog>
</template>
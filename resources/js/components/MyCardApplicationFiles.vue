<template>
    <div>
        <v-fab
            v-if = "isEditing"
            v-tooltip:top-end = "$t('file.add')"
            :aria-label = "$t('file.add')"
            absolute
            class = "mr-3"
            color = "primary"
            icon = "mdi-file-plus"
            location = "right"
            offset
            @click = "fileAdd"
        />
        <my-new-or-edit-file
            ref = "fileDialog"
            @add-file = "newFile"
            :file-options = "fileOptions"
        />
        <v-container :aria-label = "$t('document', 2)" fluid max-width = "30rem">
            <my-show-file
                v-for = "(file, index) in files"
                :key = "'files-' + index"
                :file = "file"
                :is-academic = "isAcademic"
                :is-previewing = "showFileIndex === index"
                :is-temporary-saved-application = "isEditing"
                @delete = "fileDelete(file, index)"
                @edit = "fileEdit(file)"
                @hide = "fileHide()"
                @newStatus = "fileChangeStatus(file, $event)"
                @preview = "filePreview(file, index)"
            />
        </v-container>
    </div>
</template>

<script lang = "ts">
import MyShowFile from "./MyShowFile.vue";
import MyNewOrEditFile from "./MyNewOrEditFile.vue";
import type CardApplication from "@models/CardApplication";
import type CardApplicationDocument from "@models/CardApplicationDocument";
import type { CardDocumentStatusEnum } from "@enums/CardDocumentStatusEnum";
import { mapMutations } from "vuex";

export default {
	name :       "MyCardApplicationFiles",
	emits :      [
		/**
		 * Emitted when a file is previewed.
		 * @event preview
		 * @type {?string} link - The preview link.
		 */
		"preview",
	],
	components : {
		MyNewOrEditFile,
		MyShowFile,
	},
	props :      {
		application : {
			type : Object as () => CardApplication,
			required : true,
		},
		loadings :    {
			type : Array as () => boolean[],
			required : true,
		},
		isAcademic : {
			type :    Boolean,
			default : () => true,
		},
		isApplicationPeriodOpen : {
			type :    Boolean,
			default : true,
		},
	},
	data() {
		return {
			/**
			 * The list of files from the application model that the user sees.
			 */
			files : [] as CardApplicationDocument[],
			/**
			 * Index of the file currently being previewed
			 */
			showFileIndex : null as number | null,

			filesCanAdd : [],
		};
	},
	computed : {
		/**
		 * Is The application currently Editting
		 */
		isEditing() {
			return this.application.isEditing && this.isApplicationPeriodOpen;
		},

		fileOptions() {
			return this.filesCanAdd.map( key => ( {
				value : key,
				title : this.$t( "backend.files." + key + ".short" ),
			} ) );
		},
	},
	methods : {
		/**
		 * Fetches document data from the backend or from vuex.
		 */
		async getDocuments() {
			this.loadings.push( true );
			try {
				const files = await this.application.getDocuments();
				this.files = [ ...files ];
			} finally {
				this.loadings.pop();
			}
		},
		async getFilesCanAdd() {
			if ( !this.isEditing ) return [];
			const locale = this.$i18n.locale;  // Get the current locale
			const translations = this.$i18n.messages[ locale ][ "backend" ]?.[ "files" ]; // Access the 'files' group for the current locale
			if ( translations )
				return Object.keys( translations );
			this.loadings.push( true );
			await this.getTranslation()
			          .finally(
				          () => this.loadings.pop(),
			          );
			return this.getFilesCanAdd();
		},
		...mapMutations( "files", [
			"setPreviewUrl",
		] ),
		async getTranslation( lang = null ) {
			lang ??= this.$i18n.locale;
			try {
				const { data } = await this.$axios.get( this.route( "lang", { lang : lang } ) );
				this.addTranslations( lang, { "backend" : data } );
			} catch ( error ) {
				throw error;
			}
		},
		addTranslations( locale, translations ) {
			const i18n = this.$i18n;
			console.info( i18n );
			if ( !i18n.messages[ locale ] ) {
				i18n.messages[ locale ] = {};
			}
			i18n.setLocaleMessage( locale, {
				...i18n.messages[ locale ],
				...translations,
			} );
		},

		/**
		 * Adds a new file to the application and updates the UI.
		 * @param document - The new file object.
		 */
		newFile( document : CardApplicationDocument ) {
			console.log( document, document.file );
			this.loadings.push( true );
			this.application.addNewFile( document )
			    .then( () =>
				           this.files.push( document ),
			    )
			    .catch( ( error ) => {
				            this.$refs.fileDialog.reOpenAddFileDialog( document );
				            throw error;
			            },
			    )
			    .finally( () => {
				              this.loadings.pop();
			              },
			    )
		},

		/**
		 * Opens the dialog to add a new file.
		 */
		fileAdd() {
			console.log( "fileAdd" );
			if ( this.$refs.fileDialog ) {
				this.$refs.fileDialog.openAddFileDialog();
			}
		},

		/**
		 * Updates the status of a file.
		 * @param file - The file to update.
		 * @param status - The new status.
		 */
		fileChangeStatus( file : CardApplicationDocument, status : CardDocumentStatusEnum ) {
			file.status = status;
			console.log( "changeStatus", status );
		},

		/**
		 * Deletes a file from the application and removes it from the list.
		 * @param {CardApplicationDocument} file - The file to delete.
		 * @param {number} index - The index of the file in the list.
		 */
		fileDelete( file : CardApplicationDocument, index : number ) {
			this.application.deleteFile( file );
			this.files.splice( index, 1 );
			console.log( "fileDelete", index );
		},

		/**
		 * Opens the dialog to edit an existing file.
		 * @param {CardApplicationDocument} file - The file to edit.
		 */
		fileEdit( file : CardApplicationDocument ) {
			if ( this.$refs.fileDialog ) {
				this.$refs.fileDialog.openEditFileDialog( file );
			}
			console.log( "fileEdit" );
		},

		/**
		 * Hides the currently previewed file.
		 */
		fileHide() {
			this.$emit( "preview", null );
			this.showFileIndex = null;
			this.setPreviewUrl( null );

		},

		/**
		 * Previews the selected file.
		 * @param {CardApplicationDocument} file - The file to preview.
		 * @param {number} index - The index of the file.
		 */
		filePreview( file : CardApplicationDocument, index : number ) {
			console.log( "filePreview", file );
			if ( file.file ) {
				const reader = new FileReader();
				reader.onload = ( e ) => {
					const docLink = e.target?.result;
					this.$emit( "preview", docLink );
					this.setPreviewUrl( docLink );
					this.showFileIndex = index;
					console.log( index );
				};
				reader.readAsDataURL( file.file );
				return;
			}
			if ( file.id > 0 ) {
				const docLink = this.route( "document.show", { document : file.id } );
				this.$emit( "preview", docLink );
				this.setPreviewUrl( docLink );
				this.showFileIndex = index;
			}
		},

	},

	async created() {
		this.getDocuments();
		this.filesCanAdd = await this.getFilesCanAdd();
	},
};
</script>

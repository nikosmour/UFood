<template>
    <v-card
        v-if = "application" :loading = "isLoading" :title = " $t('documents.upload')"
        class = "justify-content-around"
    >
        <MyCardApplicationFiles
            :application = "application"
            :loadings = "loadings"
        />
        
        <v-textarea
            v-model = "comment"
            :counter = "maxChars"
            :label = "$t('comment.value')"
            :rules = "rules"
            auto-grow
            clearable
            variant = "outlined"
        
        
        />
        <v-card-actions class = "justify-space-between">
            <v-btn
                :disabled = "isLoading"
                :text = "$t('save')"
                color = "primary"
                variant = "elevated"
                @click = "saveApplication"
            />
            <v-btn
                :disabled = "isLoading"
                :text = "$t('submit')"
                color = "primary"
                variant = "elevated"
                @click = "submitDocuments"
            />
        </v-card-actions>
    
    </v-card>
</template>
<script lang = "ts">
import MyCardApplicationFiles from "@components/MyCardApplicationFiles.vue";
import { CardStatusEnum } from "@enums/CardStatusEnum";
import CardApplicationDocument from "@models/CardApplicationDocument";
import { CardDocumentStatusEnum } from "@enums/CardDocumentStatusEnum";
import type CardApplication from "@models/CardApplication";

export default {
	name :       "DocumentEdit",
	components : { MyCardApplicationFiles },
	props :      {
		application : {
			type :     Object as () => CardApplication,
			required : true,
		},
	},
	data() {
		const maxCommentChar = 220;
		return {
			loadings : [],
			maxChars : maxCommentChar,
			comment :  "",
			rules :    [
				v => v.length <= maxCommentChar || this.$t( "validation.lte.string", {
					attribute : this.$t( "comment" ),
					value :     maxCommentChar,
				} ),
			],
		};
	},
	computed : {
		isLoading() : Boolean {
			return this.loadings.length !== 0;
		},
	},
	methods :  {
		saveApplication( event : Event | null, status = CardStatusEnum.TEMPORARY_SAVED ) {
			console.log( "status", status );

			if ( !Array.isArray( this.application.card_application_document ) ||
			     this.application.card_application_document.length === 0 )
				return alert( "you haven't upload any files" );
			if ( -1 !== ( this.application.card_application_document as CardApplicationDocument[] )
				.findIndex( ( obj ) => obj.status === CardDocumentStatusEnum.INCOMPLETE,
				) )
				return alert( "there are incomplete files  update their description or delete them" );
			const documents = this.getDocumentsForUpdate(); // Renamed for clarity
			const url = this.route( "cardApplication.update", this.application.id );

			/*const params = new FormData();
			params.append( "status", status.value );
			params.append( "documents.update", JSON.stringify( documents.update ) );
			params.append( "documents.delete[]", documents.delete);

			params.append( "_method", "PUT" );*/

			const params = {
				status :  status.value,
				_method : "PUT",
				documents,
			};
			if ( this.comment )
				params[ "comment" ] = this.comment;
			console.log( params, documents );
			this.loadings.push( true );
			this.$axios.post( url, params )
			    .then( () => {
				    // Update application status
				    this.application.card_last_update = {
					    status :  status,
					    comment : null,
				    };
				    // Handle document deletions
				    if ( documents.delete.length ) {
					    const idsToDeleteSet = new Set( documents.delete );
					    this.application.card_application_document =
						    ( this.application.card_application_document as CardApplicationDocument[] )
							    .filter( document => !idsToDeleteSet.has( document.id ) );
				    }
				    // Update the documents' status property to Submitted
				    if ( documents.update.length ) {
					    const idsToUpdateSet = new Set( documents.update.map( ( item ) => item.id ) );
					    ( this.application.card_application_document as CardApplicationDocument[] )
						    .forEach( ( document ) => {
							              if ( idsToUpdateSet.has( document.id ) )
								              document.status = CardDocumentStatusEnum.SUBMITTED;
						              },
						    );
				    }
			    } )
			    .catch( ( error ) => {
				    // Handle request error
				    console.error( "Error updating application:", error );
				    alert( "An error occurred while saving the application." );
			    } )
			    .finally( () => this.loadings.pop() );
		},

		getDocumentsForUpdate() {
			console.log( "getDocumentsForUpdate" );
			console.log( this.application.card_application_document );
			const documentsToUpdate = ( this.application.card_application_document as CardApplicationDocument[] )
				.filter( application => application.change !== null );
			const result = {
				update : [] as Array<{ id : number, description : string }>,
				delete : [] as Array<number>,
			};
			console.log( documentsToUpdate );
			documentsToUpdate.forEach( ( document ) => {
				console.log( document );
				if ( document.change === CardApplicationDocument._DELETE ) {
					result.delete.push( document.id );
				} else {
					result.update.push( {
						                    id :          document.id,
						                    description : document.description,
					                    } );
				}
			} );

			return result;
		},

		submitDocuments() {
			this.saveApplication( null, CardStatusEnum.SUBMITTED );
		},


	},

};
</script>
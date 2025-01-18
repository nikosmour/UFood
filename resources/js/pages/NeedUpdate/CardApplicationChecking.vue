<template>
    <card-application-abstract class = "text-center align-center ">
        <card-application-checking-search v-if = "isSearch" :applications = "applications" @getId = "getId($event)" />
        <v-progress-linear :active = "isLoading" indeterminate height = "5rem" color = "primary" />
        <v-container max-width = "50em">
                <v-btn-group v-if = "cursor.data">

                    <!-- Previous Page Button -->
                    <v-btn
                        v-if = "cursor.prev_cursor"
                        class = "mr-5"
                        color = "secondary"
                        @click = "prevPage"
                    >
                        {{ $t( "previous" ) }}
                    </v-btn>
                    <!-- Next Page Button -->
                    <v-btn
                        v-if = "cursor.next_cursor"
                        color = "primary"
                        @click = "nextPage"
                    >
                        {{ $t( "next" ) }}
                    </v-btn>
                </v-btn-group>
                <CardApplicationShowData :application = "selectedItem" />
            </v-container>
    </card-application-abstract>
</template>

<script>
import CardApplicationShowData from "@pages/NeedUpdate/CardApplicationChecking/CardApplicationShowData.vue";
import CardApplicationAbstract from "@components/CardApplicationAbstract.vue";
import CardApplicationCheckingSearch from "@pages/NeedUpdate/CardApplicationChecking/CardApplicationCheckingSearch.vue";
import CardApplication from "@models/CardApplication";

export default {
	components : {
		CardApplicationCheckingSearch,
		CardApplicationShowData,
		CardApplicationAbstract,
	},
	data() {
		return {
			cursor :       { data : [] },
			selectedItem : null,
			loading : [],
		};
	},
	computed : {
		isLoading() {
			return this.loading.length !== 0;
		},
		isSearch() {
			return this.$route.name === "cardApplication.checking.search";
		},
		category() {
			return this.$route.params.category
			       ? this.$enums.CardStatusEnum[ this.$route.params.category.toUpperCase() ].value
			       : null;
		},
		applicationId() {
			return this.$route.params.application || this.$route.query.application;
		},
		applications() {
			return this.cursor.data;
		},
	},
	methods :  {
		broadcasting() {
			console.log( "capdApplicationChecking.vue, broadcasting  " );
			if ( typeof this.$echo !== "undefined" && this.category )
				this.$echo.join( `cardChecking.${ this.category.replace( " ", "_" ) }` )
				    .here( ( users ) => console.log( "Joined channel", users ) )
				    .joining( ( user ) => console.log( "Joining", user ) )
				    .leaving( ( user ) => console.log( "Leaving", user ) )
				    .error( ( error ) => console.error( error ) )
				    .listen( "CardApplicationUpdated", this.updateApplicationsIds );
			else
				console.log( " Echo:", this.$echo, " this.category:", this.category );
		},
		getId( formData ) {
			this.getApplications( formData[ 0 ], formData[ 1 ] )
			    .then( applications => {
				    if ( applications[ 0 ] ) {
					    this.$router.replace( {
						                          name :   this.$route.name,
						                          params : { category : this.category },
						                          query :  { "application" : applications[ 0 ].id },
					                          } );
				    }
			    } );
		},
		async getApplicationById( id ) {
			const position = this.applications.findIndex( obj => obj.id === id );
			return this.selectedItem = position === -1
			                           ? ( await this.getApplications( "application_id", id ) )[ 0 ]
			                           : this.applications[ position ];
		},
		async getApplications( name, value, url = this.route( "cardApplication.checking.search" ) ) {
			try {
				this.loading.push( true );
				console.info( "test" );
				const response = await this.$axios.get( url, { params : { [ name ] : value } } );
				console.log( "get Applications", response.data );
				const applications = response.data.data;
				const success = applications.length > 0;
				if ( success ) {
					let cursor = response.data;
					this.$notify( {
						              error : this.$t( "card.application.found", applications.length ),
						              color : "info",
					              } );
					console.info( cursor.data );
					cursor.data = applications.map( ( application ) => new CardApplication( application ) );

					if ( name !== "application_id" ) {
						this.cursor = cursor;
						// let applicationsLength = ( cursor.next_page_url || cursor.prev_page_url )
						//                          ? 2
						//                          : applications.length;
					}

					return cursor.data;

				}
				this.$displayError( {
					                    error : this.$t( "card.application.found", 0 ),
					                    color : "success",
				                    } );
				return [ null ];
			} catch ( errors ) {
				console.log( errors );
				return [ null ];
			} finally {
				this.loading.pop();
			}
		},
		async startingData() {
			console.info( "cardApplicationChecking.startingData", this.category, this.$route.params.category );
			console.info( "eotueotueonuthseohutnthoenuoetnhueotuho", this.category );
			if ( this.category ) {
				const applications = await this.getApplications( "status", this.category );
				if ( !this.applicationId && applications.length > 0 && applications[ 0 ] ) {
					this.$router.replace( {
						                      name :   this.$route.name,
						                      params : { category : this.category },
						                      query : { "application" : applications[ 0 ].id },
					                      } );
				}
			}
			if ( this.applicationId )
				this.getApplicationById( this.applicationId );
		},
		updateApplicationsIds( e ) {
			const cardApplicationId = e.cardApplication_id;
			const status = e.status;
			const position = this.applications.findIndex( obj => obj.id >= cardApplicationId );

			if ( position !== -1 )
				if ( status !== this.category )
					this.applications.splice( position, 1 );
				else
					this.applications.splice( position, 0, { id : cardApplicationId } );
			else if ( status === this.category )
				this.applications.push( { id : cardApplicationId } );
		},
		nextPage() {
			if ( Number( this.applicationId ) === Number( this.applications[ 0 ]?.id ) )
				return this.changePage( this.cursor.next_page_url );
			this.$router.replace( {
				                      name : this.$route.name,
				                      params : { category : this.category },
				                      query : { "application" : this.applications[ 0 ]?.id },
			                      } );
		},
		prevPage() {
			this.changePage( this.cursor.prev_page_url );
		},
		async changePage( url ) {
			if ( url ) {
				const applications = await this.getApplications( "status", this.category, url );
				if ( applications[ 0 ] ) {
					this.$router.replace( {
						                      name :   this.$route.name,
						                      params : { category : this.category },
						                      query :  { "application" : applications[ 0 ]?.id },
					                      } );
				}
			}
		},
	},
	mounted() {
		console.info( "mounted " );
		this.startingData();
		// this.broadcasting();
	},
	watch : {
		category( newValue, oldValue ) {
			this.applications = [];
			if ( newValue )
				this.startingData();
			else
				this.selectedItem = null;
			// this.broadcasting();
			if ( typeof this.$echo !== "undefined" && oldValue ) this.$echo.leave( `cardChecking.${ oldValue }` );
		},
		async applicationId( newValue ) {
			if ( newValue ) {
				return this.getApplicationById( newValue );
			}
			if ( this.route.name === "cardApplication.checking" )
				this.startingData();
		},
	},
	beforeRouteLeave() {
		if ( typeof this.$echo !== "undefined" && this.category ) this.$echo.leave( `cardChecking.${ this.category }` );
	},
};
</script>

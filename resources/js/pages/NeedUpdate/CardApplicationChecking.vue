<template>
    <v-container class = "text-center ">
        <router-view :applications = "applications" class = "col" @getId = "getId($event)" />

        <v-container class = "col" max-width = "50em">
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
    </v-container>
</template>

<script>
import CardApplicationShowData from "@pages/NeedUpdate/CardApplicationChecking/CardApplicationShowData.vue";
import Message from "@pages/NeedUpdate/CardApplicationChecking/Message.vue";

export default {
	components : {
		Message,
		CardApplicationShowData,
	},
	data() {
		return {
			cursor :       { data : [] },
			selectedItem : null,
			result :       {
				message : "",
				success : true,
				hide :    false,
				errors :  [],
			},
		};
	},
	computed : {
		category() {
			return this.$route.params.category
			       ? this.$enums.CardStatusEnum[ this.$route.params.category.toUpperCase() ].value
			       : this.$enums.CardStatusEnum.SUBMITTED.value;
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
		async getApplications( name, value, url = this.route( "cardApplication.checking.search" ) ) {
			try {
				console.info( "test" );
				const response = await this.$axios.get( url, { params : { [ name ] : value } } );
				console.log( "get Applications", response.data );
				const applications = response.data.data;
				const success = this.result.success = applications.length > 0;
				if ( name !== "application_id" ) {
					let cursor = this.cursor = response.data;
					let applicationsLength = ( cursor.next_page_url || cursor.prev_page_url )
					                         ? 2
					                         : applications.length;
					this.result.message = ( success
					                        ? ""
					                        : this.$t( "request_failed" ) + ": " ) +
					                      this.$t( "application", applicationsLength ) + " " +
					                      this.$t( "found", applicationsLength )
					                          .toLowerCase();
				}
				this.result.errors = [];

				return applications.length > 0
				       ? applications
				       : [ null ];
			} catch ( errors ) {
				console.log( errors );
				this.result.message = this.$t( "request_failed" ) + ": " + this.$t( "application", 0 ) + " " +
				                      this.$t( "found", 0 )
				                          .toLowerCase();

				this.result.success = false;
				return [ null ];
			}
		},
		async startingData() {
			console.info( "cardApplicationChecking.startingData", this.category );
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
			this.selectedItem = this.applicationId
			                    ? ( await this.getApplications( "application_id", this.applicationId ) )[ 0 ]
			                    : null;
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
			this.changePage( this.cursor.next_page_url );
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
			this.startingData();
			// this.broadcasting();
			if ( typeof this.$echo !== "undefined" && oldValue ) this.$echo.leave( `cardChecking.${ oldValue }` );
		},
		async applicationId( newValue ) {
			if ( newValue ) {
				const position = this.applications.findIndex( obj => obj.id == newValue );
				this.selectedItem = position === -1
				                    ? ( await this.getApplications( "application_id", newValue ) )[ 0 ]
				                    : this.applications[ position ];
			}
		},
	},
	beforeRouteLeave( to, from ) {
		if ( typeof this.$echo !== "undefined" && this.category ) this.$echo.leave( `cardChecking.${ this.category }` );
	},
};
</script>

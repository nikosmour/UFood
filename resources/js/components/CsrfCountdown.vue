<template>
    <v-btn
        :color = "color"
        class = "d-flex justify-center text-center"
        v-on:click = "updateCookie"
    >
        {{ formattedTime }}
    </v-btn>
</template>

<script lang = "ts">
import { mapActions, mapGetters, mapMutations } from "vuex";
import { defineComponent } from "vue";

export default defineComponent( {
	                                name :  "CsrfCountdown",
	                                props : {
		                                warningSecond : {
			                                type :    Number,
			                                default : () => 60 * 5,
		                                },
		                                errorSecond :   {
			                                type :    Number,
			                                default : () => 60,
		                                },
	                                },
	                                data() {
		                                return {
			                                interval :  0,
			                                stopWatch : null as ( () => void ) | null,
		                                };
	                                }
	                                ,
	                                computed : {
		                                ...mapGetters( "session", [ "getTimeLeft" ] ),
		                                formattedTime() : string {
			                                const getTimeLeft = this.getTimeLeft;
			                                const minutes = Math.floor( getTimeLeft / 60 );
			                                const seconds = getTimeLeft % 60;
			                                return `${ minutes.toString()
			                                                  .padStart( 2, "0" ) }:${ seconds
				                                .toString()
				                                .padStart( 2, "0" ) }`;
		                                },
		                                color() : string {
			                                return this.getTimeLeft > this.warningSecond
			                                       ? ""
			                                       : ( this.getTimeLeft > this.errorSecond )
			                                         ? "warning"
			                                         : "error";
		                                },

	                                }
	                                ,
	                                methods : {
		                                ...mapMutations( "session", [ "setTimeLeft" ] ),
		                                ...mapActions( "session", [ "updateCookies" ] ),
		                                startCountdown() {
			                                this.interval = window.setInterval( () => {
				                                if ( this.getTimeLeft > 0 ) {
					                                this.setTimeLeft( this.getTimeLeft - 1 );
				                                } else {
					                                clearInterval( this.interval as number );
					                                this.stopWatch = this.$watch( "getTimeLeft", () => {
						                                this.startCountdown();
						                                ( this.stopWatch as ( () => void ) )();
					                                } );
				                                }
			                                }, 1000 );
		                                },
		                                updateCookie() {
			                                this.updateCookies( {
				                                                    route : this.route,
				                                                    axiosInstance : this.$axios,
			                                                    } );
		                                },
	                                },

	                                mounted() {
		                                this.startCountdown();
	                                },

	                                beforeUnmount() {
		                                if ( this.interval !== null ) {
			                                clearInterval( this.interval );
		                                }
	                                },
                                } );

</script>

<style scoped>
h1 {
    font-size: 3rem;
    font-weight: bold;
}
</style>

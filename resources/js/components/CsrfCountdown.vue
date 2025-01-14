<template>
    <v-btn
        :aria-label = "$t('csrf_countDown.toLogout')+': ' + formattedTime"
        :color = "color"
        class = "d-flex justify-center text-center"
        v-on:click = "updateCookie"
        aria-live = "polite"
        role = "timer"
    >
        {{ formattedTime }}
    </v-btn>
    <v-dialog v-model = "isErrorTime" max-width = "30em">
        <v-card :title = "$t('session.expireSoon.title')">
            <v-card-text>
                {{ $t( "session.expireSoon.details", { time : formattedTime } ) }}
            </v-card-text>
            <v-card-actions class = "justify-end">
                <v-btn
                    color = "primary"
                    @click = "updateCookie"
                >
                    {{ $t( "refresh" ) }}
                </v-btn>
            </v-card-actions>
        </v-card>
    </v-dialog>
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
		                                isErrorTime() {
			                                return this.getTimeLeft < this.errorSecond;
		                                },

	                                }
	                                ,
	                                methods : {
		                                ...mapMutations( "session", [ "setTimeLeft" ] ),
		                                ...mapActions( "session", [ "updateCookies" ] ),
		                                startCountdown() {
			                                this.interval = window.setInterval( () => {
				                                if ( this.getTimeLeft > 1 ) {
					                                this.setTimeLeft( this.getTimeLeft - 1 );
				                                } else {
					                                this.setTimeLeft( 0 );
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

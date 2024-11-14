<script>
import { mapState } from "vuex";

export default {
	name : "FreeFoodStatus",

	computed : {
		...mapState(
			{
				/**
				 * Card status of the user.
				 * @returns {EnumUnit | null}
				 */
				card_status( state ) {
					return state.auth.user?.card_applicant?.current_card_application?.card_last_update?.status ||
					       null;
				},

				/**
				 * Expiration date of the user's valid card application.
				 * @returns {string | null}
				 */
				card_expiration_date( state ) {
					return state.auth.user?.card_applicant?.valid_card_application?.expiration_date ||
					       null;
				},
			} ),

		/**
		 * Constructs the card status text based on the current card status and expiration date.
		 * @returns {string}
		 */
		cardStatus() {
			let text = this.$t( "applied.not" );

			if ( this.card_status ) {
				text = this.$t( "latest-application-is", {
					status : this.$t( `status.${ this.card_status.key.toLowerCase() }` )
					             .toLowerCase(),
				} );
			}

			if ( this.card_expiration_date ) {
				text += ` ${ this.$t( "expiration.date.is", {
					date : new Date( this.card_expiration_date ).toLocaleDateString(),
				} ) }`;
			}

			return text;
		},
	},
};
</script>

<template>
    <v-container>
        <v-card :aria-label = "$t('card.status')" :title = "$t('card')">
            <v-card-item>
                <v-list>
                    <v-list-item
                        :title = "cardStatus"
                    />
                </v-list>
            </v-card-item>
            <v-card-actions class = "d-flex justify-space-around">
                <v-btn
                    v-if = "$route.name !== 'card.History'"
                    :text = "$t('history')"
                    :to = "{ name: 'card.History' }"
                    color = "primary"
                    variant = "elevated"
                />
                <v-btn
                    v-if = "$route.name !== 'card.application'"
                    :text = "$t('application')"
                    :to = "{ name: 'card.application' }"
                    color = "primary"
                    variant = "elevated"
                />
            </v-card-actions>
        </v-card>
    </v-container>
</template>

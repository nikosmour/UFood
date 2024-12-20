<script lang = "ts">
import { mapGetters } from "vuex";

export default {
	name : "MyFreeFoodStatus",

	computed : {
		...mapGetters( "auth", {
			card_status :          "cardStatus",
			card_expiration_date : "cardExpirationDate",
			application : "cardApplication",
		} ),

		/**
		 * Constructs the card status text based on the current card status and expiration date.
		 */
		card_info() : string {
			let text = this.$t( "applied.not" );

			if ( this.card_status ) {
				const statusKey = this.card_status.key?.toLowerCase();
				const statusTranslation = this.$t( `status.${ statusKey }` )
				                              .toLowerCase();

				text = this.$t( "application-status-is.current", {
					status : statusTranslation,
				} );
			}

			if ( this.card_expiration_date ) {
				const expirationDate = this.card_expiration_date;
				const today = new Date();
				today.setHours( 0, 0, 0, 0 );

				if ( this.card_expiration_date >= today ) {
					text += ` ${ this.$t( "approved.until", {
						date : expirationDate.toLocaleDateString(),
					} ) }`;
				}
			}
			text += "\n" + ( this.application.card_staff_update_latest?.comment ?? "" );
			return text;
		},

		/**
		 * Returns the alert type based on the current card status.
		 */
		alert_type() : "error" | "success" | "warning" | "info" | undefined {
			const CardStatusEnum = this.$enums.CardStatusEnum;
			return {
				[ CardStatusEnum.ACCEPTED ] :          "success",
				[ CardStatusEnum.INCOMPLETE ] :        "warning",
				[ CardStatusEnum.REJECTED ] :          "error",
				[ CardStatusEnum.SUBMITTED ] :         "info",
				[ CardStatusEnum.TEMPORARY_SAVED ] :   "info",
				[ CardStatusEnum.CHECKING ] :          "info",
				[ CardStatusEnum.TEMPORARY_CHECKED ] : "info",
			}[ this.card_status ];
		},

		/**
		 * Checks if the current route name matches "card.application".
		 * @returns {boolean}
		 */
		isCardApplicationRoute() {
			return this.$route.name === "card.application";
		},
	},
};
</script>

<template>
    <v-alert
        v-if = "isCardApplicationRoute"
        :text = "card_info"
        :type = "alert_type"
        closable
    />
    <v-container v-else>
        <v-card :aria-label = "$t('card.info')" :title = "$t('card.value')">
            <v-card-item>
                <v-list>
                    <v-list-item
                        :title = "card_info"
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

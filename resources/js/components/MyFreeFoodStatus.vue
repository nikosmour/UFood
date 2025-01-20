<script lang = "ts">
import { mapGetters } from "vuex";

export default {
	name : "MyFreeFoodStatus",
	data() {
		return {
			showApplication : false,
		};
	},

	computed : {
		...mapGetters( "auth", {
			card_status :          "cardStatus",
			card_expiration_date : "cardExpirationDate",
			application : "cardApplication",
			user : "currentUser",
		} ),

		/**
		 * Constructs the card status text based on the current card status and expiration date.
		 */
		card_info_current() : string {
			if ( this.card_status ) {
				const statusKey = this.card_status.key?.toLowerCase();
				const statusTranslation = this.$t( `status.${ statusKey }` )
				                              .toLowerCase();

				return this.$t( "application-status-is.current", {
					status : statusTranslation,
				} );
			}
			return this.$t( "applied.not" );
		},
		card_info_expiration() : string {

			if ( this.card_expiration_date ) {
				const expirationDate = this.card_expiration_date;
				const today = new Date();
				today.setHours( 0, 0, 0, 0 );

				if ( this.card_expiration_date >= today ) {
					return ` ${ this.$t( "approved.until", {
						date : expirationDate.toLocaleDateString( "en-ca" ),
					} ) }`;
				}
			}
			return "";
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
		isCardApplicationRoute() : boolean {
			return this.$route.name === "card.application";
		},
	},
	methods :  {
		isActivePeriod() : boolean {
			// Step 1: Check if lastDate is defined and valid
			const lastDate = this.config?.application?.lastDate;

// If lastDate is undefined, null, or not a valid date, set canSubmit to false
			if ( !lastDate || isNaN( ( new Date( lastDate ) ).getTime() ) ) {
				return false;
			}
			// Step 2: Convert lastDate to a Date object
			const untilDate = new Date( lastDate );

			// Step 3: Get the current date and strip the time portion for comparison
			const currentDate = new Date();
			currentDate.setHours( 0, 0, 0, 0 ); // Set time to 00:00:00.000 to only compare the date part

			// Strip the time portion of untilDate as well
			untilDate.setHours( 0, 0, 0, 0 );

			// Step 4: Perform the comparison
			return currentDate <= untilDate;
		},
	},
	created() {
		this.showApplication = !!this.application || this.isActivePeriod();
	},

};
</script>

<template>
    <v-alert
        v-if = "isCardApplicationRoute"
        :type = "alert_type"
        closable
    >
        <span v-if = "card_info_current">{{ card_info_current }}<br /></span>
        <span v-if = "card_info_expiration">{{ card_info_expiration }}<br /></span>
        <span
            v-if = "application?.card_staff_update_latest?.comment && application?.card_staff_update_latest?.status!==$enums.CardStatusEnum.ACCEPTED"
        >
    {{ application?.card_staff_update_latest?.comment }}
  </span>
    </v-alert>
    <v-container v-else>
        <v-card :aria-label = "$t('card.info')" :title = "$t('card.value')">
            <v-card-item class = "text-center">
                <span v-if = "card_info_current">{{ card_info_current }}<br /></span>
                <span v-if = "card_info_expiration">{{ card_info_expiration }}<br /></span>
            </v-card-item>
            <v-card-actions class = "d-flex justify-space-around">
                <v-btn
                    v-if = "$route.name !== 'card.History' && !!user.card_applicant"
                    :text = "$t('history')"
                    :to = "{ name: 'card.History' }"
                    color = "primary"
                    variant = "elevated"
                    prepend-icon = "mdi-history"
                />
                <v-btn
                    v-if = "showApplication && ($route.name !== 'card.application')"
                    :text = "$t('application')"
                    :to = "{ name: 'card.application' }"
                    color = "primary"
                    variant = "elevated"
                    append-icon = "mdi-application"
                />
            </v-card-actions>
        </v-card>
    </v-container>
</template>

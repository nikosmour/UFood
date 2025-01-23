<script lang = "ts">
import { defineComponent } from "vue";
import { mapActions } from "vuex";
import type { AxiosInstance } from "axios";

export default defineComponent(
	{
		name :    "CreateUser",
		data :    function () {
			return {
				url :       this.route( "user.store" ),
				isLoading : false,
			};
		},
		methods : {
			...mapActions( "auth", [
				"logout",
				"getUser",
			] ),
			async createUser() {
				if ( this.isLoading )
					return;
				try {
					this.isLoading = true;
					const response = await ( this.$axios as AxiosInstance ).post( this.url );
					await this.getUser( response );
					if ( this.$route.query.redirect )
						this.$router.push( this.$route.query.redirect );
				} catch ( errors ) {
					throw errors;
				} finally {
					this.isLoading = false;
				}
			},
		},

	} );
</script>

<template>
    <div class = "ml-0 pl-0">
        <v-card-title class = "mb-4">
            <span class = "text-h6">{{ $t( "personalPrivacy" ) }}</span>
        </v-card-title>
        <v-card-text>
            <div id = "personal_data_info" class = "mb-5">
                {{ $t( "personalData.explain" ) }}
                <!--
                 <p>
                                Το Πανεπιστήμιο Πατρών( ΠΠ ) σέβεται βαθύτατα την σπουδαιότητα της
                                προστασίας των δεδομένων προσωπικού χαρακτήρα των φυσικών προσώπων και
                                της σύννομης και ορθής επεξεργασίας τους.Προσηλωμένο λοιπόν σε αυτόν το
                                σκοπό, την κατοχύρωση της προστασίας των δεδομένων προσωπικού χαρακτήρα
                                των φυσικών προσώπων, συμμορφώνεται πλήρως με τις βασικές αρχές
                                επεξεργασίας προσωπικών δεδομένων, λαμβάνοντας πρωτίστως υπόψη τα
                                δικαιώματα των φυσικών προσώπων και διασφαλίζει ότι τα δεδομένα
                                προσωπικού χαρακτήρα, τα οποία βρίσκονται στην κατοχή του συλλέγονται
                                νομίμως για καθορισμένους, με απόλυτο τρόπο σκοπούς. < /p>


                                <v-divider class="my-4"></v-divider>

                                                                        < p > Κατά το χρονικό διάστημα της λειτουργίας της εφαρμογής, η
                                πρόσβαση στα δεδομένα προσωπικού χαρακτήρα ασκείται με τη δυνατότητα
                                εξαγωγής σχετικής αναφοράς, σε εκτυπώσιμη μορφή, μέσω της εφαρμογής.Κατά το ίδιο χρονικό διάστημα, η διόρθωση τυχόν ανακριβών δεδομένων που
                                υποβάλλονται από τα υποκείμενα των δεδομένων κατά τη χρήση της εφαρμογής
                                πραγματοποιείται επίσης μέσω της εφαρμογής.Η διόρθωση δεδομένων, τα
                                οποία ήδη τηρούνται στο ΠΠ και τα οποία εμφανίζονται σε προσυμπληρωμένα
                                πεδία της εφαρμογής είναι δυνατή μόνο με την υποβολή σχετικής αίτησης
                                στο οικείο Ακαδημαϊκό Τμήμα. < /p>
                                                      < ul >
                                                      <li>
                                                          Σε περίπτωση παραβίασης των δικαιωμάτων για την προστασία των
                                δεδομένων προσωπικού χαρακτήρα, τα υποκείμενα των δεδομένων έχουν το
                                δικαίωμα να υποβάλουν καταγγελία στην Αρχή Προστασίας Δεδομένων
                                Προσωπικού Χαρακτήρα( www.dpa.gr ).
                                           < /li>
                                           < li >
                                           Για τα θέματα που αφορούν την προστασία των δεδομένων προσωπικού
                                χαρακτήρα τα οποία επεξεργάζεται το ΠΠ υπάρχει η δυνατότητα επικοινωνίας
                                με τον Υπεύθυνο Προστασίας Δεδομένων του Πανεπιστημίου στην ηλεκτρονική
                                διεύθυνση dpo@upatras.gr και στο τηλέφωνο 2610962018.
                                                                          < /li>
                                                                          < /ul>

                                                                          < br >
                -->
            </div>
            <v-row class = "d-flex justify-space-between mr-2 ml-2 mt-2 mb-2">
                <v-btn
                    :aria-description = " $t('personalData.reject')"
                    :loading = "isLoading" :text = "$t( 'reject' )" color = "secondary"
                    @click = "logout"
                    aria-details = "personal_data_info"
                />
                <v-btn
                    :aria-description = " $t('personalData.accept')"
                    aria-details = "personal_data_info"
                    :loading = "isLoading" :text = "$t( 'accept' )" color = "primary"
                    type = "sumbit" variant = "elevated"
                    @click = "createUser"

                />
            </v-row>
        </v-card-text>
    </div>
</template>

<style scoped>

</style>
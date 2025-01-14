<template>
    <v-container>
        <v-card :loading = "isLoading" class = "pa-5" @keydown.ctrl.alt.space = "showLastTransaction">
            <v-card-text>
                <v-form
                    ref = "entryForm" v-model = "isValid" :aria-label = "$t('entry_check_form')"
                    validate-on = "input lazy"
                    @submit.prevent = "check_id"
                >
                    <v-text-field
                        v-model.number.trim = "academic_id"
                        :class = "resultClass"
                        :error-messages = "errors.academic_id"
                        :label = "$t('id')"
                        :rules = "rules.academic_id"
                        autofocus
                        max-width = "20em"
                        outlined
                        required
                        type = "number"

                        @input = "handleBarcodeInput();resetAcademicIdInputState();"
                    />
                </v-form>
                <v-alert
                    :aria-hidden = "!show"
                    :class = "{'opacity-100': show, 'opacity-0': !show, 'mt-4': true}"
                    :title = "result.message"
                    :type = "result.success ? 'success' : 'error'"
                    max-width = "20em"
                    dismissible
                    @input = "hideAlert"
                />
            </v-card-text>
            <v-card-actions class = "justify-center">
                <v-btn
                    :disabled = "!isValid"
                    :loading = "isLoading"
                    :text = "$t('submit')"
                    color = "primary"
                    @click = "check_id"
                />
            </v-card-actions>
        </v-card>
    </v-container>
</template>
<script>
export default {
	emits : [
		"newEntry",
	],
	data() {
		return {
			academic_id :              "",
			errors :                   { academic_id : null },
			isLoading :                false,
			isValid :                  false,
			isViewingLastTransaction : false,
			lastTransaction :          {
				errors :  { academic_id : null },
				id :      null,
				message : null,
				success : null,
			},
			show :                     false,
			time :                     2500,
			result :                   {
				message : "",
				success : true,
			},
			url :                      this.route( "entryChecking.store" ),
			typingTimer : null,
		};
	},
	computed : {
		/**
		 * Returns the class based on the result of the operation.
		 * 'text-green' for success, 'text-red' for failure, or an empty string if not shown.
		 * @returns {string} The class to apply.
		 */
		resultClass() {
			return this.show
			       ? ( this.result.success
			           ? "text-green"
			           : "text-red" )
			       : "";
		},

		/**
		 * Defines the validation rules for the academic ID field.
		 * @returns {Object} The validation rules for the academic_id field.
		 */
		rules() {
			return {
				academic_id : [
					v => !!v || this.$t( "validation.required", { "attribute" : this.$t( "id" ) } ),
					v => v > 0 || this.$t( "validation.exists", { "attribute" : this.$t( "id" ) } ),
					// Prevent resubmission of the same ID unless it's viewing the last transaction or has passed enough time after last transaction
					v => v !== this.lastTransaction.id || this.isViewingLastTransaction || !this.show ||
					     this.$t( "validation.entry_same_user" ),
				],
			};
		},
	},
	methods :  {
		/**
		 * Handles the process of checking the academic ID. This includes validating the form, sending a request
		 * to check the academic ID, and processing the response.
		 *
		 * @async
		 * @function check_id
		 * @returns {Promise<void>}
		 * @description
		 * 1. Clears previous error states by calling resetAcademicIdInputState`.
		 * 2. Validates the form and stops execution if the form is not valid.
		 * 3. Sends a POST request with the academic ID and processes the response.
		 * 4. Updates the result message based on the success or failure of the request.
		 * 5. Handles API response errors and updates the error messages for the academic ID.
		 * 6. Emits a new entry event upon successful submission.
		 * 7. Handles UI state, showing a loading indicator and updating the last transaction details.
		 */
		async check_id() {
			// 1.Reset errors before validation
			if ( !this.academic_id ) return;
			this.resetAcademicIdInputState();
			// 2.Validate form before proceeding
			if ( !( await this.$refs.entryForm.validate() ).valid ) return;
			if ( this.isLoading ) return;
			this.isLoading = true;
			if ( this.typingTimer )
				clearTimeout( this.typingTimer );
			// Set the last transaction details
			this.lastTransaction = { id : this.academic_id };

			// Prepare and send the request
			const params = new FormData();
			params.append( "academic_id", this.academic_id );

			//3. Sends a POST request
			try {
				const response = await this.$axios.post( this.url, params );
				const passWith = response.data.passWith;
				this.result.success = true;
				this.result.message = this.$t( "entry." + passWith );
				this.$emit( "newEntry", `${ passWith }s` ); // 6.Emitting event
			} catch ( error ) {
				this.result.success = false;
				//5. handles the error responses
				if ( error.response?.status === 422 || error.response?.status === 409 ) {
					this.result.message = this.$t( "entry.notAccept" );
					this.errors = error.response.data.errors;
				} else {
					// this.result.message = this.$t( "entry.notAccept" );
					throw error;
				}
			} finally {
				//Handles UI state, showing a loading indicator and updating the last transaction details.
				this.isLoading = false;
				this.lastTransaction = {
					...this.result,
					id :     this.academic_id,
					errors : { ...this.errors },
				};
				this.lastTransaction.errors.academic_id = this.errors.academic_id;
				this.show = true;

				// Hide the alert and reset after a short time
				this.resetAfterTimeout();
			}
		},

		/**
		 * Hide the Alert
		 */
		hideAlert() {
			this.show = false;
		},

		/**
		 * Show the previous transaction details
		 */
		showLastTransaction() {
			this.isViewingLastTransaction = true;
			this.result.success = this.lastTransaction.success;
			this.result.message = this.lastTransaction.message;
			this.errors.academic_id = this.lastTransaction.errors.academic_id;
			this.academic_id = this.lastTransaction.id;
			this.show = true;
			this.resetAfterTimeout();
		},

		/**
		 * Clears the academic ID error, resets related state, and hides any active alerts.
		 */
		resetAcademicIdInputState() {
			// Reset the viewing transaction flag
			this.isViewingLastTransaction = false;

			// Clear any validation error for the academic ID
			this.errors.academic_id = null;

			// Hide the alert message
			this.hideAlert();
		},
		handleBarcodeInput() {

			// Check if the scanner sends a complete barcode with an Enter key
			// Clear the previous timer
			if ( this.typingTimer )
				clearTimeout( this.typingTimer );
			/* if (this.academic_id.endsWith('\n') || this.academic_id.endsWith('\r')) {
				this.check_id() // Remove newline/carriage return characters
			} */

			// Start a new timer to detect when input is complete
			this.typingTimer = setTimeout( () => {
				this.check_id(); // Submit the barcode after delay
			}, 400 );
		},

		/**
		 * Clears the academic ID error, resets related state,
		 * hides any active alerts, resets the form
		 */
		resetAfterTimeout() {
			setTimeout( () => {
				this.resetAcademicIdInputState();
				this.$refs.entryForm.reset();
			}, this.time );
		},
	},
};
</script>

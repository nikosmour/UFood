<template>
    <v-list-item
        :border = "'b-xl s-xl '+ styleColor"
        :subtitle = "name"
        :title = "description"
        class = "mt-2"
        prepend-icon = "mdi-paperclip"
    >
        <template #append>
            <v-btn-group>
                <template v-for = "(icon, index) in icons" :key = "'icons-'+index">
                    <v-btn
                        :aria-hidden = "icon.isHidden"
                        :aria-label = "$t(icon.ariaLabel)"
                        :class = "icon.isHidden ? 'opacity-0' : 'opacity-100'"
                        :color = "icon.color"
                        :disabled = "icon.isHidden"
                        v-tooltip:top = "$t(icon.ariaLabel)"
                        class = "mr-1"
                        icon
                        size = ""

                        variant = "text"
                        @click = "iconClick(icon.actionOrStatus)"
                    >
                        <v-icon :icon = "icon.icon" size = "x-large" />
                    </v-btn>
                </template>
            </v-btn-group>
        </template>
    </v-list-item>
</template>

<script lang = "ts">
import type CardApplicationDocument from "@models/CardApplicationDocument";
import type { CardDocumentStatusEnum } from "@enums/CardDocumentStatusEnum";
import type { PropertyType } from "@types/globals";


type colorStyle = "success" | "error" | "warning" | "primary";
/**
 * @component MyShowFile
 * @description Displays a file item with dynamic actions based on user type and file status.
 *
 * @emits
 * @event preview - Emitted when the preview button is clicked.
 * @event new-status - Emitted when the status of the file is updated.
 *   @param {string} newStatus - The new status of the file.
 * @event edit - Emitted when the edit button is clicked (academic only).
 * @event delete - Emitted when the delete button is clicked (academic only).
 *
 * @properties
 * @property {string} description - The description of the file. (Required)
 * @property {string} name - The name of the file. (Required)
 * @property {CardDocumentStatusEnum} status - The status of the file. Possible values: "ACCEPTED", "SUBMITTED", "REJECTED", "INCOMPLETE". (Required)
 * @property {boolean} isAcademic - Indicates if the user is academic. (Required)
 */
export default {
	name : "MyShowFile",

	emits : [
		/**
		 * Emitted when the hide button is clicked.
		 * @event hide
		 */
		"hide",
		/**
		 * Emitted when the preview button is clicked.
		 * @event preview
		 */
		"preview",

		/**
		 * Emitted when the status of the file is updated.
		 *
		 * @event new-status
		 * @type {CardDocumentStatusEnum} newStatus - The new status of the file.
		 */
		"new-status",

		/**
		 * Emitted when the edit button is clicked (academic only).
		 * @event edit
		 */
		"edit",

		/**
		 * Emitted when the delete button is clicked (academic only).
		 * @event delete
		 */
		"delete",
	],

	props : {
		/**
		 * Indicates if the user is academic.
		 * @type {boolean}
		 * @required
		 */
		isAcademic : {
			type :     Boolean,
			required : true,
		},
		/**
		 * Indicates if the application is temporarily saved and not yet submitted.
		 * When true, academic users must press "edit" before accessing file editing options,
		 * and the application status will transition to "TemporarySave."
		 * @type {boolean}
		 * @default true
		 */
		isTemporarySavedApplication : {
			type :    Boolean,
			default : () => true,
		},

		/**
		 * Indicates if the application is temporarily saved and not yet submitted.
		 * When true, academic users must press "edit" before accessing file editing options,
		 * and the application status will transition to "TemporarySave."
		 * @type {boolean}
		 * @default true
		 */
		isPreviewing : {
			type :    Boolean,
			default : () => false,
		},

		/**
		 * The file.
		 * @type {CardApplicationDocument}
		 * @required
		 */
		file : {
			type : Object as CardApplicationDocument,
			required : true,
		},

	},

	computed : {
		/**
		 * The description of the file.
		 */
		description() : string | null {
			return this.$t( "backend.files." + this.file.description + ".short" );
		},

		/**
		 * The name of the file.
		 * @required
		 */
		name() : string | null {
			return this.file.file_name;
		},

		/**
		 * @returns The status of the file.
		 */
		status() : PropertyType<CardDocumentStatusEnum> {
			return this.file.status;
		},


		/**
		 * @typedef {Object} iconType
		 * @property {boolean} isHidden Determines whether the icon should be hidden (`true`) or visible (`false`).
		 * @property {String|null} color Specifies the button's color (e.g., "primary", "success"). Use `null` for no specific color.
		 * @property {String} icon Name of the Material Design icon to display (e.g., "mdi-eye", "mdi-pencil").
		 * @property {String | CardDocumentStatusEnum} actionOrStatus The emitted action name (e.g., "edit", "delete") or status value (e.g., "ACCEPTED").
		 * @property {String} ariaLabel A text description of the icon's action for screen readers.
		 */

		/**
		 * Generates dynamic icons based on the user type (`isAcademic`) and file status.
		 *
		 * @returns {Array<iconType>} An array of icon configurations relevant to the user type and status.
		 */
		icons() {
			const status = this.status;
			const statusEnum = this.$enums.CardDocumentStatusEnum;
			const academicIcons = [
				{
					isHidden :       false,
					color :          "primary",
					icon :           !this.isPreviewing
					                 ? "mdi-eye"
					                 : "mdi-eye-off",
					actionOrStatus : !this.isPreviewing
					                 ? "preview"
					                 : "hide",
					ariaLabel :      !this.isPreviewing
					                 ? "file.preview"
					                 : "file.hide", // Accessibility label
				},
				{
					isHidden :       !this.isTemporarySavedApplication ||
					                 ![
						                 statusEnum.SUBMITTED,
						                 statusEnum.INCOMPLETE,
						                 null,
					                 ].includes( status ),
					color :          null,
					icon :           "mdi-pencil",
					actionOrStatus : "edit",
					ariaLabel : "file.edit",
				},
				{
					isHidden :       !this.isTemporarySavedApplication ||
					                 ![
						                 statusEnum.SUBMITTED,
						                 statusEnum.INCOMPLETE,
						                 null,
					                 ].includes( status ),
					color :          null,
					icon :           "mdi-delete",
					actionOrStatus : "delete",
					ariaLabel :      "file.delete",
				},
			];
			const staffIcons = [
				{
					isHidden :       false,
					color :          "primary",
					icon :      !this.isPreviewing
					            ? "mdi-eye"
					            : "mdi-eye-off",
					actionOrStatus : "preview",
					ariaLabel : !this.isPreviewing
					            ? "file.preview"
					            : "file.hide",
				},
				{
					isHidden :       status === statusEnum.ACCEPTED,
					color :          "success",
					icon :           "mdi-check",
					actionOrStatus : statusEnum.ACCEPTED,
					ariaLabel :      "file.markAccepted",
				},
				{
					isHidden :       status === statusEnum.INCOMPLETE,
					color :          "warning",
					icon :           "mdi-alert-octagon-outline",
					actionOrStatus : statusEnum.INCOMPLETE,
					ariaLabel :      "file.markIncomplete",
				},
				{
					isHidden :       status === statusEnum.REJECTED,
					color :          "error",
					icon :           "mdi-close-circle",
					actionOrStatus : statusEnum.REJECTED,
					ariaLabel :      "file.markRejected",
				},
			];

			return ( this.isAcademic )
			       ? academicIcons
			       : staffIcons;

		},
		/**
		 * Computes the style color based on the current status.
		 *
		 * @returns {String} A Vuetify theme color corresponding to the current status.
		 *
		 * The returned color is mapped as follows:
		 * - "success": For the status `ACCEPTED`.
		 * - "primary": For the status `SUBMITTED`.
		 * - "error": For the status `REJECTED`.
		 * - "warning": For the status `INCOMPLETE`.
		 */
		styleColor() {
			const statusEnum = this.$enums.CardDocumentStatusEnum;

			const styles = {
				[ statusEnum.ACCEPTED ] :   "success",   // Green for accepted status.
				[ statusEnum.SUBMITTED ] :  "primary", // Blue for submitted status.
				[ statusEnum.REJECTED ] :   "error",    // Red for rejected status.
				[ statusEnum.INCOMPLETE ] : "warning", // Yellow for incomplete status.
			};

			// Return the color corresponding to the current status.
			return styles[ this.status ] || "default"; // Fallback to "default" if no match.
		},
	},

	methods : {
		/**
		 * Handles click events for action icons.
		 * @param {string|CardDocumentStatusEnum} actionOrStatus - The action or new status for the file.
		 * @fires MyShowFile#new-status
		 */
		iconClick( actionOrStatus ) {
			const statusEnum = this.$enums.CardDocumentStatusEnum.toArray();

			if ( statusEnum.includes( actionOrStatus ) ) {
				/**
				 * new status event
				 *
				 * @event MyShowFile#new-status
				 * @type {CardDocumentStatusEnum}
				 */
				this.$emit( "new-status", actionOrStatus );
			} else {
				this.$emit( actionOrStatus );
			}
		},
	},
	created() {
		console.log( this.file );
	},
};

</script>

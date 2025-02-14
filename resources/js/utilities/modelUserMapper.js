// modelUserMapper.js

import Academic from "@models/Academic";
import CouponStaff from "@models/CouponStaff";
import EntryStaff from "@models/EntryStaff";
import CardApplicationStaff from "@models/CardApplicationStaff";

// Map of model names to JavaScript classes
const modelMapping = {
	Academic :             Academic,
	CouponStaff :          CouponStaff,
	EntryStaff :           EntryStaff,
	CardApplicationStaff : CardApplicationStaff,
};

/**
 * Get the JavaScript model class based on the model name.
 * @param { keyof typeof modelMapping} modelClassName - The name of the model (e.g., "Academic").
 * @returns {typeof import("./BaseModel") |null} - The corresponding model class or null if not found.
 */
export function getModelClass( modelClassName ) {
	return modelMapping[ modelClassName ] || null;
}

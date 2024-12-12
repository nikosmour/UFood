import AddressBase from "./Base/AddressBase";

/**
 * Class representing a Address model.
 * @class
 * @extends AddressBase
 */
export class Address extends AddressBase {
	static findModelInArray( searchData, array ) {
		return array.find( item => item.is_permanent === searchData.is_permanent ) || null;
	}
}

export default Address;
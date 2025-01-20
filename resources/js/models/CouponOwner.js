import CouponOwnerBase from "./Base/CouponOwnerBase";
import { EchoInstance } from "@/plugins/echo.js";
import CouponTransaction from "./CouponTransaction";

/**
 * Class representing a CouponOwner model.
 * @class
 * @extends CouponOwnerBase
 */
export class CouponOwner extends CouponOwnerBase {
	broadcast( options = {} ) {
		super.broadcast( options );
		const echo = options.vue.$echo;
		
		const couponOwner = options.target;
		const $notify = options.vue.$notify;
		console.info( options );
		const meals = options.vue.$enums.MealPlanPeriodEnum;
		const channelName = `academic.${ this.academic_id }`;
		console.info( "CouponOwnerBroadCasting" );
		echo
			.private( channelName )
			.listen( "CouponOwnerUpdated", ( e ) => {
				const transaction = new CouponTransaction( e[ "couponTransaction" ] );
				for ( const meal in meals ) {
					couponOwner[ meal ] += transaction[ meal ];
					transaction[ `total.${ meal }` ] = couponOwner[ meal ];
				}
				couponOwner.updated_at = transaction[ "created_at" ];
				if ( couponOwner.coupon_transactions )
					couponOwner.coupon_transactions.unshift( transaction );
				console.info( "CouponOwnerUpdated", e, this, transaction );
				$notify( { error : "new Coupon Transaction" } );
			} );
	}
	
	stopBroadcast() {
		super.stopBroadcast();
		const channelName = `cardApplication.${ this.id }`;
		EchoInstance.private( channelName )
		            .stopListening( "CouponOwnerUpdated" );
	}
}

export default CouponOwner;
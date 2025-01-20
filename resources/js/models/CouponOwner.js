import CouponOwnerBase from "./Base/CouponOwnerBase";
import CouponTransaction from "./CouponTransaction";
import { connectEcho } from "@/plugins/echo.js";

/**
 * Class representing a CouponOwner model.
 * @class
 * @extends CouponOwnerBase
 */
export class CouponOwner extends CouponOwnerBase {
	broadcast( options = {} ) {
		super.broadcast( options );
		const echo = connectEcho();
		
		const couponOwner = options.target;
		const $notify = options.vue.$notify;
		console.info( options );
		const meals = options.vue.$enums.MealPlanPeriodEnum;
		const $t = options.vue.$t;
		const channelName = `academic.${ this.academic_id }`;
		console.info( "CouponOwnerBroadCasting" );
		echo
			.private( channelName )
			.listen( "CouponOwnerUpdated", ( e ) => {
				const transaction = couponOwner.manageNewTransaction( e[ "couponTransaction" ], meals, couponOwner );
				console.info( "CouponOwnerUpdated", e, this, transaction );
				$notify( {
					         error : $t( "transaction.newNotify", {
						                     transaction : $t( "transaction." + transaction.transaction ),
					                     },
					         ),
				         } );
			} );
	}
	
	manageNewTransaction( newTransaction, meals, couponOwner ) {
		const transaction = new CouponTransaction( newTransaction );
		for ( const meal in meals ) {
			couponOwner[ meal ] += transaction[ meal ];
			transaction[ `total.${ meal }` ] = couponOwner[ meal ];
		}
		couponOwner.updated_at = transaction[ "created_at" ];
		if ( couponOwner.coupon_transactions )
			couponOwner.coupon_transactions.unshift( transaction );
		return transaction;
	}
	
	stopBroadcast() {
		super.stopBroadcast();
		const channelName = `cardApplication.${ this.id }`;
		const EchoInstance = connectEcho();
		EchoInstance.private( channelName )
		            .stopListening( "CouponOwnerUpdated" );
	}
}

export default CouponOwner;
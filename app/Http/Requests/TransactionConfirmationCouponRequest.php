<?php

namespace App\Http\Requests;

use App\Models\Academic;
use App\Models\CouponStaff;

class TransactionConfirmationCouponRequest extends TransactionRequest
{
    /**
     * Determine if the user is authorized to make this request.
     * If the user has the ability to sell coupons.
     * @return bool
     */
    public function authorize(): bool
    {
        $user = auth()->user();
        return ($user instanceof Academic) || ($user instanceof CouponStaff);
    }

}

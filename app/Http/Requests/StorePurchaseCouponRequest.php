<?php

namespace App\Http\Requests;

class StorePurchaseCouponRequest extends TransactionRequest
{
    /**
     * Determine if the user is authorized to make this request.
     * If the user has the ability to sell coupons.
     * @return bool
     */
    public function authorize(): bool
    {
        return true;
    }

}

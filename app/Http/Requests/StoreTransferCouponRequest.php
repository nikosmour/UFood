<?php

namespace App\Http\Requests;

class StoreTransferCouponRequest extends TransactionRequest
{
    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize(): bool
    {
        return true;
    }
}

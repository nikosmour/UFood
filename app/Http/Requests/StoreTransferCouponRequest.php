<?php

namespace App\Http\Requests;

class StoreTransferCouponRequest extends TransactionRequest
{
    public function __construct()
    {
        parent::__construct();
        $this->user = auth()->user();
    }
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

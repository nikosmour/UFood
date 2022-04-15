<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class TransferCoupon extends Model
{
    use HasFactory;
    public $guarded=[];
    public $timestamps = ['create_at'];//todo false if not work only one
    public function setUpdatedAt($value)
    {
        return;
    }
    public function sender(): \Illuminate\Database\Eloquent\Relations\BelongsTo
    {
        return $this->belongsTo('App\CouponOwner','sender_id');
    }
    public function receiver(): \Illuminate\Database\Eloquent\Relations\BelongsTo
    {
        return $this->belongsTo('App\CouponOwner','receiver_id');
    }

}

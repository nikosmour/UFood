<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class UsageCoupon extends Model
{
    use HasFactory;
    public $timestamps = false;

    public function couponOwner(): \Illuminate\Database\Eloquent\Relations\BelongsTo
    {
        return $this->belongsTo(CouponOwner::class,'academic_id');
    }
    public function entryStaff(): \Illuminate\Database\Eloquent\Relations\BelongsTo
    {
        return $this->belongsTo(EntryStaff::class);
    }
}

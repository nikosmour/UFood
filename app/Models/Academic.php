<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

/**
 * @mixin IdeHelperAcademic
 */
class Academic extends User
{
    public $incrementing = false;
    protected $primaryKey = 'academic_id';

    public function cardApplicant(): \Illuminate\Database\Eloquent\Relations\HasOne
    {
        return $this->hasOne(CardApplicant::class,'academic_id');
    }
    public function couponOwner(): \Illuminate\Database\Eloquent\Relations\HasOne
    {
        return $this->hasOne(CouponOwner::class,'academic_id');
    }
}

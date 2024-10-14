<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Propaganistas\LaravelPhone\Casts\E164PhoneNumberCast;

/**
 * @mixin IdeHelperAddress
 */
class Address extends Model
{
    use HasFactory;

    protected $casts = [
        'phone' => E164PhoneNumberCast::class . ':GR'
    ];
    #public $timestamps = false;
    public function cardApplicant()
    {
        return $this->belongsTo(CardApplicant::class, 'academic_id');
    }
}

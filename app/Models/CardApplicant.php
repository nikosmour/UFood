<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

/**
 * @mixin IdeHelperCardApplicant
 */
class CardApplicant extends Model
{
    use HasFactory;
    public $incrementing = false;
    protected $primaryKey = 'academic_id';

    public function academic(): \Illuminate\Database\Eloquent\Relations\BelongsTo
    {
        return $this->belongsTo(Academic::class,'academic_id');
    }
    public function cardApplication(): \Illuminate\Database\Eloquent\Relations\HasMany
    {
        return $this->hasMany(CardApplication::class,'academic_id');
    }
    public function usageCard(): \Illuminate\Database\Eloquent\Relations\HasMany
    {
        return $this->hasMany(UsageCard::class,'academic_id')->orderByDesc('date',);
    }
    protected $casts = [
        'year'=>'date:Y',
        'expiration_date'=>'date:Y-m-d',
    ];
}

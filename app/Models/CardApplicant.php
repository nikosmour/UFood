<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

/**
 * @mixin IdeHelperCardApplicant
 */
class CardApplicant extends Model
{
    use HasFactory;

    public $incrementing = false;
    protected $primaryKey = 'academic_id';
    /**
     * The attributes that should be cast.
     * @var string[]
     */
    protected $casts = [
        'year' => 'date:Y',
    ];

    public function academic(): BelongsTo
    {
        return $this->belongsTo(Academic::class,'academic_id');
    }

    public function cardApplication(): HasMany
    {
        return $this->hasMany(CardApplication::class);
    }

    public function usageCard(): HasMany
    {
        return $this->hasMany(UsageCard::class)->orderByDesc('date',);
    }

    public function address(): HasMany
    {
        return $this->hasMany(Address::class)->orderByDesc('created_at');
    }
}

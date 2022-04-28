<?php

namespace App\Models;

use App\Enum\CardStatusEnum;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class CardApplication extends Model
{
    use HasFactory;

    /**
     * The attributes that should be cast.
     * @var string[]
     */
    protected $casts = [
        'status' => CardStatusEnum::class,
    ];

    public function cardApplicant(): BelongsTo
    {
        return $this->belongsTo(CardApplicant::class, 'academic_id');
    }

    public function cardApplicationDocument(): HasMany
    {
        return $this->hasMany(CardApplicationDocument::class);
    }

    public function cardApplicationStaff(): BelongsTo
    {
        return $this->belongsTo(CardApplicationStaff::class);
    }
}

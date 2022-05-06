<?php

namespace App\Models;

use App\Enum\CardStatusEnum;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;

/**
 * @mixin IdeHelperCardApplication
 */
class CardApplication extends Model
{
    use HasFactory;

    /**
     * The attributes that should be cast.
     * @var string[]
     */
    protected $casts = [
        'status' => CardStatusEnum::class,
        'expiration_date' => 'date:Y-m-d',
    ];

    public function cardApplicant(): BelongsTo
    {
        return $this->belongsTo(CardApplicant::class, 'academic_id');
    }

    public function cardApplicationDocument(): HasMany
    {
        return $this->hasMany(CardApplicationDocument::class);
    }

    public function cardApplicationStaff(): BelongsToMany
    {
        return $this->belongsToMany(CardApplicationStaff::class)->using(CardApplicationChecking::class);
    }

    public function staffComments(): HasMany
    {
        return $this->hasMany(CardApplicationChecking::class);
    }

    public function applicantComments(): HasMany
    {
        return $this->hasMany(HasCardApplicantComment::class);
    }
}

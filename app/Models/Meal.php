<?php

namespace App\Models;

use App\Enum\MealCategoryEnum;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

/**
 * @mixin IdeHelperMeal
 */
class Meal extends Model
{
    use HasFactory;

    /**
     * The attributes that should be cast.
     * @var string[]
     */
    protected $casts = [
        'category' => MealCategoryEnum::class,
    ];

    public function meal(): BelongsToMany
    {
        return $this->belongsToMany(MealPlan::class);
    }
}

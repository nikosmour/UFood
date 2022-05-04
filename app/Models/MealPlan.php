<?php

namespace App\Models;

use App\Enum\MealPlanPeriodEnum;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\belongsToMany;

/**
 * @mixin IdeHelperMealPlan
 */
class MealPlan extends Model
{
    use HasFactory;

    public $timestamps = false;
    protected $dateFormat = 'y-m-d';
    /**
     * The attributes that should be cast.
     * @var string[]
     */
    protected $casts = [
        'date' => 'date:Y-m-d',
        'period' => MealPlanPeriodEnum::class,
    ];


    /**
     * @return belongsToMany
     */
    public function meal(): BelongsToMany
    {
        return $this->belongsToMany(Meal::class);
    }
}

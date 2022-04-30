<?php

namespace App\Models;

use App\Models\Scopes\DailyMealScope;
use App\Models\Scopes\FromTodayScope;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Prunable;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

/**
 * @method static withoutGlobalScopes()
 */
class DailyMealPlan extends Model
{
    use Prunable;

    protected $table = 'meal_plans';
    protected $with = ['breakfast', 'lunch', 'dinner'];
    protected $primaryKey ='date';
    protected $keyType = 'string';

    protected static function booted()
    {
        static::addGlobalScope(new DailyMealScope());
        static::addGlobalScope(new FromTodayScope('date'));
    }

    public function breakfast(): BelongsToMany
    {
        return $this->belongsToMany(Meal::class, 'meal_meal_plan', 'meal_plan_id', parentKey: 'breakfast_id');
    }

    public function lunch(): BelongsToMany
    {
        return $this->belongsToMany(Meal::class, 'meal_meal_plan', 'meal_plan_id', parentKey: 'lunch_id');
    }

    public function dinner(): BelongsToMany
    {
        return $this->belongsToMany(Meal::class, 'meal_meal_plan', 'meal_plan_id', parentKey: 'dinner_id');
    }

    /**
     * Get the prunable model query.
     *
     * @return Builder
     */
    public function prunable(): Builder
    {
        return static::withoutGlobalScopes()->where('date', '<=', now()->subDay());
    }
    protected $casts = [
        'date' => 'date:Y-m-d',
    ];


}

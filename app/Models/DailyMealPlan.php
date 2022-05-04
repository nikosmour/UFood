<?php

namespace App\Models;

use App\Models\Scopes\DailyMealScope;
use App\Models\Scopes\FromTodayScope;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\MassPrunable;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

/**
 * @method static withoutGlobalScopes()
 */
class DailyMealPlan extends Model
{
    use MassPrunable;

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
        return $this->belongsToMany(Meal::class, 'meal_meal_plan', 'meal_plan_id', parentKey: 'breakfast_id')->orderBy('category')->orderBy('id');
    }

    public function lunch(): BelongsToMany
    {
        return $this->belongsToMany(Meal::class, 'meal_meal_plan', 'meal_plan_id', parentKey: 'lunch_id')->orderBy('category')->orderBy('id');
    }

    public function dinner(): BelongsToMany
    {
        return $this->belongsToMany(Meal::class, 'meal_meal_plan', 'meal_plan_id', parentKey: 'dinner_id')->orderBy('category')->orderBy('id');
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

<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Meal extends Model
{
    use HasFactory;

    public function meal(): \Illuminate\Database\Eloquent\Relations\HasMany
    {
        return $this->belongsToMany(MealPlan::class);
    }
}

<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class MealPlan extends Model
{
    use HasFactory;
    public $timestamps = false;
    protected $dateFormat = 'y-m-d';

    public function meal(): \Illuminate\Database\Eloquent\Relations\belongsToMany
    {
        return $this->belongsToMany(Meal::class);
    }
    protected $casts = [
        'date'=>'date:Y-m-d',
    ];
}

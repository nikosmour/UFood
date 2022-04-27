<?php

namespace App\Models;

use App\Traits\MealPlanTrait;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class UsageCard extends Model
{
    use HasFactory, MealPlanTrait;

    public $incrementing = false;
    public $timestamps = false;
    public $fillable = ['academic_id'];
    protected $primaryKey = ['date', 'academic_id', 'type'];

    protected $casts = [
        'date' => 'date:Y-m-d',
        'time' => 'date:H:m',
    ];
    protected $dates = [
        'date',
        'time',
    ];

    protected static function boot()
    {
        parent::boot();
        // define default date time and entry_staff_id when model is created
        static::creating(function ($model) {
            if ($model->isClean('date'))
                $model->date = now();
            if ($model->isClean('time'))
                $model->time = now();
            if ($model->isClean('status'))
                $model->status = $model->getCurrentMealPeriod();
            if ($model->isClean('entry_staff_id'))
                /** @noinspection PhpUndefinedFieldInspection */ $model->entry_staff_id = auth()->user()->entryStaff->id;


        });
    }

    public function cardApplicant(): BelongsTo
    {
        return $this->belongsTo(CardApplicant::class, 'academic_id');
    }

    public function entryStaff(): BelongsTo
    {
        return $this->belongsTo(EntryStaff::class);
    }

}

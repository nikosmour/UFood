<?php

namespace App\Models;

use App\Traits\MealPlanTrait;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class UsageCoupon extends Model
{
    use HasFactory,MealPlanTrait;

    public $timestamps = false;
    public $fillable = ['academic_id'];

    protected static function boot()
    {
        parent::boot();
        // define default date time and entry_staff_id when model is created
        static::creating(function ($model) {
            if ($model->isClean('status'))
                $model->status = $model->getCurrentMealPeriod();
            if ($model->isClean('entry_staff_id'))
                /** @noinspection PhpUndefinedFieldInspection */ $model->entry_staff_id = auth()->user()->entryStaff->id;
            $model->couponOwner->decrement($model->status);
        });
    }

    public function couponOwner(): BelongsTo
    {
        return $this->belongsTo(CouponOwner::class, 'academic_id');
    }

    public function entryStaff(): BelongsTo
    {
        return $this->belongsTo(EntryStaff::class);
    }
}

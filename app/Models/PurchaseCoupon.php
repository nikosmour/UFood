<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Facades\DB;

/**
 * @mixin IdeHelperPurchaseCoupon
 */
class PurchaseCoupon extends Model
{
    use HasFactory;

    public $guarded = [];
    public $timestamps = false;

    protected static function boot()
    {
        parent::boot();
        static::creating(
        /**
         * define default sender_id when model is created and
         * adding the coupons to the buyer
         * @param $model
         * @return void
         */
            function ($model) {
                if ($model->isClean('coupon_staff_id'))
                    /** @noinspection PhpUndefinedFieldInspection */ $model->coupon_staff_id = auth()->user()->id;
                CouponOwner::addCoupons($model['academic_id'], $model->attributes);
            });
    }

    public function getMoneyAttribute($money): float|int
    {
        return $money / 100;
    }

    public function setMoneyAttribute($money)
    {
        $this->attributes['money'] = $money * 100;
    }

    public function couponOwner(): BelongsTo
    {
        return $this->belongsTo(CouponOwner::class, 'academic_id');
    }

    public function couponStaff(): BelongsTo
    {
        return $this->belongsTo(CouponStaff::class);
    }

    public function scopeTakeStatistics(Builder $query, $vData)
    {
        $translate_staff = __('transactions.coupon_staff');
        $translate_student = __('transactions.coupon_student');

        $table_Prefix = DB::getTablePrefix();
        $purchaseTable = (new PurchaseCoupon())->getTable();
        $academicTable = (new Academic())->getTable();
        $selectColumns = [
            DB::raw('DATE(' . $table_Prefix . $purchaseTable . '.created_at) as date'),
            DB::raw("(CASE WHEN " . $table_Prefix . $academicTable . ".status = 'researcher' THEN '$translate_staff' ELSE '$translate_student' END) as category"),
            //            DB::raw('sum(money) as money')
        ];

        foreach ($vData['meal_category'] as $period) {
            $selectColumns[$period] = DB::raw("SUM($period) as {$period}");
        }

        return $query->select($selectColumns)
            ->join($academicTable, $purchaseTable . '.academic_id', '=', $academicTable . '.academic_id')
            ->whereBetween(DB::raw('DATE(' . $table_Prefix . $purchaseTable . '.created_at)'), [
                $vData['from_date'],
                $vData['to_date']
            ])
            ->groupBy('date', 'category');
    }


}

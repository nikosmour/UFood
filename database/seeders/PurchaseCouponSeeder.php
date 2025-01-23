<?php

namespace Database\Seeders;

use App\Enum\MealPlanPeriodEnum;
use App\Models\CouponOwner;
use App\Models\CouponStaff;
use App\Models\PurchaseCoupon;
use Database\Seeders\Classes\ManyToManySeeder;
use Database\Seeders\Traits\ReorderRowsTrait;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;

class PurchaseCouponSeeder extends ManyToManySeeder
{
    use ReorderRowsTrait;
    use WithoutModelEvents;
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $this->make_connection(
            CouponOwner::whereDoesntHave('purchaseCoupon')->cursor(),
            CouponStaff::all(),
            PurchaseCoupon::class, $this->count, true);
//        $this->reorderRows(PurchaseCoupon::class,'created_at');
        $array = [];
        foreach (MealPlanPeriodEnum::names() as $period) {
            $array[$period] = 10;
        }
        PurchaseCoupon::where('id', '>', 0)->update($array);
    }
}

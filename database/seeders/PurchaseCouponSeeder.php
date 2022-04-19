<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class PurchaseCouponSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $length=5;
        $couponOwners=\App\Models\CouponOwner::all();
        $couponStaffs=\App\Models\CouponStaff::all();
        foreach ( $couponOwners as $buyer){
            for ($i = $length ; $i >0 ;$i--)
                \App\Models\PurchaseCoupon::factory()->for(
                    $buyer)->for($couponStaffs->random())->create();
        }
    }
}

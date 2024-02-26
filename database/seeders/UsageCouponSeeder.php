<?php

namespace Database\Seeders;

use Database\Seeders\Classes\ManyToManySeeder;

class UsageCouponSeeder extends ManyToManySeeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $this->make_connection(
            \App\Models\CouponOwner::all(),
            \App\Models\EntryStaff::all(),
            \App\Models\UsageCoupon::class, 5);
    }
}

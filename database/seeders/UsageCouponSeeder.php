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
            \App\Models\CouponOwner::where('created_at', '>', $this->createdAtMoreThan)->cursor(),
            \App\Models\EntryStaff::all(),
            \App\Models\UsageCoupon::class, $this->count);
    }
}

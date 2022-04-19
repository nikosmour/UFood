<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class UserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $users_length=50;
        \App\Models\User::factory()->count($users_length)->create()->each(function ($user){
            $status=$user->status;
            if (str_contains($status, 'staff') ==0) {
                \App\Models\Academic::factory()->for($user)->create();
                \App\Models\CouponOwner::factory()->for($user->academic)->create();
                \App\Models\CardApplicant::factory()->for($user->academic)->create();
            }
            elseif (str_contains($status, 'coupon'))
                \App\Models\CouponStaff::factory()->for($user)->create();
            elseif (str_contains($status, 'card'))
                \App\Models\CardApplicationStaff::factory()->for($user)->create();
            else // same as elseif ($status='staff entry')
                \App\Models\EntryStaff::factory()->for($user)->create();
        });
        $this->call([
            TransferCouponSeeder::class,
            PurchaseCouponSeeder::class,
            UsageCardSeeder::class,
            UsageCouponSeeder::class,
        ]);
    }
}

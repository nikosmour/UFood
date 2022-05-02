<?php

namespace Database\Seeders;

use App\Enum\UserAbilityEnum;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class UserSeeder extends Seeder
{
    use WithoutModelEvents;
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run(): void
    {
        $users_length=50;
        \App\Models\User::factory()->count($users_length)->create()->each(function ($user){
            if ($user->hasAnyAbility([
                UserAbilityEnum::CARD_OWNERSHIP,
                UserAbilityEnum::COUPON_OWNERSHIP]))
                \App\Models\Academic::factory()->for($user)->create();
            if ($user->hasAbility(UserAbilityEnum::CARD_OWNERSHIP))
                \App\Models\CardApplicant::factory()->for($user->academic)->create();
            if ($user->hasAbility(UserAbilityEnum::COUPON_OWNERSHIP))
                \App\Models\CouponOwner::factory()->for($user->academic)->create();
            if ($user->hasAbility(UserAbilityEnum::COUPON_SELL))
                \App\Models\CouponStaff::factory()->for($user)->create();
            if ($user->hasAbility(UserAbilityEnum::CARD_APPLICATION_CHECK))
                \App\Models\CardApplicationStaff::factory()->for($user)->create();
            if ($user->hasAbility(UserAbilityEnum::ENTRY_CHECK))
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

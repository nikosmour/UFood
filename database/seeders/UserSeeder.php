<?php

namespace Database\Seeders;

use App\Enum\UserAbilityEnum;
use App\Enum\UserStatusEnum;
use App\Models\Academic;
use App\Models\CardApplicationStaff;
use App\Models\CouponStaff;
use Carbon\Carbon;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class UserSeeder extends Seeder
{
    use WithoutModelEvents;

    public function __construct(protected int $count = 50)
    {
    }
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run(): void
    {
        $currentDay = Carbon::now();
        if (0 == Academic::whereEmail('erasmus@example.com')->count())
            \App\Models\Academic::factory()
                ->has(\App\Models\CardApplicant::factory()->count(1))
                ->has(\App\Models\CouponOwner::factory()->count(1))
                ->create([
                'status' => UserStatusEnum::ERASMUS,
                'email' => 'erasmus@example.com'
            ]);
        if (0 == Academic::whereEmail('researcher@example.com')->count())
            \App\Models\Academic::factory()
                ->has(\App\Models\CouponOwner::factory()->count(1))
                ->create([
                'status' => UserStatusEnum::RESEARCHER,
                'email' => 'researcher@example.com'
            ]);

        if (0 == CouponStaff::whereEmail('staff_coupon@example.com')->count())
            \App\Models\CouponStaff::factory()->create([
                'status' => UserStatusEnum::STAFF_COUPON,
                'email' => 'staff_coupon@example.com'
            ]);
        if (0 == CardApplicationStaff::whereEmail('staff_card@example.com')->count())
            \App\Models\CardApplicationStaff::factory()->create([
                'status' => UserStatusEnum::STAFF_CARD,
                'email' => 'staff_card@example.com'
            ]);
        if (0 == CardApplicationStaff::whereEmail('staff_entry@example.com')->count())
            \App\Models\CardApplicationStaff::factory()->create([
                'status' => UserStatusEnum::STAFF_ENTRY,
                'email' => 'staff_entry@example.com'
            ]);
        for ($i = $this->count; $i > 0; $i--) {
            $user_status = collect(UserStatusEnum::cases())->random();

            if ($user_status->canAny([
                UserAbilityEnum::CARD_OWNERSHIP,
                UserAbilityEnum::COUPON_OWNERSHIP])) {
                $academic = \App\Models\Academic::factory()->create([
                    'status' => $user_status->value
                ]);
                if ($user_status->can(UserAbilityEnum::CARD_OWNERSHIP))
                    \App\Models\CardApplicant::factory()->for($academic)->create();
                if ($user_status->can(UserAbilityEnum::COUPON_OWNERSHIP))
                    \App\Models\CouponOwner::factory()->for($academic)->create();
            } elseif ($user_status->can(UserAbilityEnum::COUPON_SELL))
                \App\Models\CouponStaff::factory()->create([
                    'status' => $user_status->value
                ]);
            elseif ($user_status->can(UserAbilityEnum::CARD_APPLICATION_CHECK))
                \App\Models\CardApplicationStaff::factory()->create([
                    'status' => $user_status->value
                ]);
            elseif ($user_status->can(UserAbilityEnum::ENTRY_CHECK))
                \App\Models\EntryStaff::factory()->create([
                    'status' => $user_status->value
                ]);
        };
        $seeders = [
            TransferCouponSeeder::class,
            PurchaseCouponSeeder::class,
            UsageCardSeeder::class,
            UsageCouponSeeder::class,
            CardApplicantSeeder::class,
            CardApplicationCheckingSeeder::class
        ];
        foreach ($seeders as $seeder)
            $this->call($seeder, ['createdAtMoreThan' => $currentDay]);
    }
}

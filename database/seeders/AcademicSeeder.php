<?php

namespace Database\Seeders;

use App\Enum\UserAbilityEnum;
use App\Enum\UserStatusEnum;
use App\Models\Academic;
use App\Models\CardApplicant;
use App\Models\CouponOwner;
use Carbon\Carbon;
use Database\Seeders\Classes\UserSeederPreparation;

class AcademicSeeder extends UserSeederPreparation
{
    private bool $extra;

    public function __construct($count = 20, $extra = false)
    {
        parent::__construct($count);
        $this->extra = $extra;
    }

    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run(): void
    {
        $moreSeeders = [
            [
                "class" => CardApplicantSeeder::class,
                "parameters" => []
            ]
        ];
        $now = Carbon::now();
        if ($this->extra) {
            $moreSeeders = [
                ...$moreSeeders,
                [
                    "class" => TransferCouponSeeder::class,
                    "parameters" => [$now]
                ],
                [
                    "class" => PurchaseCouponSeeder::class,
                    "parameters" => [$now]
                ],
                [
                    "class" => UsageCardSeeder::class,
                    "parameters" => [$now]
                ],
                [
                    "class" => UsageCouponSeeder::class,
                    "parameters" => [$now]
                ],
                [
                    "class" => CardApplicationCheckingSeeder::class,
                    "parameters" => [$now]
                ],
            ];
        }
        $this->commonRun(
            $moreSeeders
        );
    }


    public function createUser(int $count, UserStatusEnum|null $users_status = null): void
    {
        $keys = array_keys($this->emailCounters);
        $numberOfStatus = count($keys);
        for ($i = $count; $i > 0; $i--) {
            $user_status = $users_status ?? UserStatusEnum::enumByName()[$keys[$i % $numberOfStatus]];
            $status = $user_status->name;
            $this->emailCounters[$status]++;
            $email = $this->generateEmail($status);

            $academic = Academic::factory()->create(['status' => $user_status->value, 'email' => $email, 'a_m' => $this->emailCounters[$status], 'academic_id' => $this->emailCounters[$status] + 2 * 10 ** 15,]);
            if ($user_status->can(UserAbilityEnum::CARD_OWNERSHIP)) CardApplicant::factory()->for($academic)->create();
            if ($this->extra) CouponOwner::factory()->for($academic)->create();
        }
    }

    /**
     * @param string $key
     * @return void
     */
    protected function initCounters($status): void
    {
        $key = $status->name;
        $this->emailCounters[$key] = Academic::where('status', $status->value)->where('email', 'like', strtolower($key) . '%')->orderByDesc('a_m')->value('a_m') ?? $this->emailCounters[$key];
    }

    protected function whenInit($status): bool
    {
        return $this->isAcademic($status);
    }
}

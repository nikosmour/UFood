<?php

namespace Database\Factories;

use App\Enum\UserStatusEnum;
use App\Models\CouponStaff;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<CouponStaff>
 */
class CouponStaffFactory extends UserFactory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition()
    {
        return [
            ...parent::definition(),
            'status' => UserStatusEnum::STAFF_COUPON,
        ];
    }
}

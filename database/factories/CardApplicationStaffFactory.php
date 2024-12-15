<?php

namespace Database\Factories;

use App\Enum\UserStatusEnum;
use App\Models\CardApplicationStaff;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<CardApplicationStaff>
 */
class CardApplicationStaffFactory extends UserFactory
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
            'status' => UserStatusEnum::STAFF_CARD,
        ];
    }
}

<?php

namespace Database\Factories;

use App\Enum\MealPlanPeriodEnum;
use Database\Factories\traits\CreatedAtTrait;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory
 */
class UsageCouponFactory extends Factory
{
    use CreatedAtTrait;
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition()
    {
        return [
            'period' => MealPlanPeriodEnum::random(),
        ];
    }
}

<?php

namespace Database\Factories;

use App\Enum\MealPlanPeriodEnum;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory
 */
class MealPlanFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition()
    {
        return [
            'date' => $this->faker->dateTimeBetween('now', '30 days'),
            'period' => MealPlanPeriodEnum::values()->random(),
        ];
    }
}

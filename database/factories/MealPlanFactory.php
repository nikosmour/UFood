<?php

namespace Database\Factories;

use App\Models\MealPlan;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\MealPlan>
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
            'period' => array_rand(config('constants.meal.plan.period')),
        ];
    }
}

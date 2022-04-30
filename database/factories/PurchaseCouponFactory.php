<?php

namespace Database\Factories;

use App\Enum\MealPlanPeriodEnum;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory
 */
class PurchaseCouponFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition()
    {
        $array = ['money' => $this->faker->numberBetween(0, 9999) / 100];
        foreach (MealPlanPeriodEnum::names() as $period) {
            $array[$period] = $this->faker->numberBetween(0, 255);
        }
        return $array;
    }
}

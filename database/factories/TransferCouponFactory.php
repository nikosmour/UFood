<?php

namespace Database\Factories;

use App\Enum\MealPlanPeriodEnum;
use Database\Factories\traits\CreatedAtTrait;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory
 */
class TransferCouponFactory extends Factory
{
    use CreatedAtTrait;

    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition()
    {
        $array = [];
        foreach (MealPlanPeriodEnum::names() as $period) {
            $array[$period] = $this->faker->numberBetween(0, 255);
        }
        return $array;
    }
}

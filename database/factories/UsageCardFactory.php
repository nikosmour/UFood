<?php

namespace Database\Factories;

use App\Enum\MealPlanPeriodEnum;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory
 */
class UsageCardFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition()
    {
        $time = $this->faker->time();

        return [
            'date' => $this->faker->dateTimeThisDecade(),
            'period' => MealPlanPeriodEnum::getCurrentMealPeriod($time),
            'time' => $time,
        ];
    }

    public function createdAt($min = null, $max = null): Factory
    {
        return $this->state(['date' => $this->faker->dateTimeBetween($min ?? now(), $max ?? now())->format('Y-m-d')]);
    }
}

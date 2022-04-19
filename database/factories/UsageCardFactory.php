<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\UsageCard>
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
        $periods=config('constants.meal.plan.period');
        return [
            'date' => $this->faker->date(),
            'status' => $periods[array_rand($periods)],
            'time'=> $this->faker->time(),
        ];
    }
}

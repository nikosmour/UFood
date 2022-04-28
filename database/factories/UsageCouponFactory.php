<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\UsageCoupon>
 */
class UsageCouponFactory extends Factory
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
            'period' => $periods[array_rand($periods)],
        ];
    }
}

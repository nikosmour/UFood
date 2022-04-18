<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\CouponOwner>
 */
class CouponOwnerFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition()
    {
        return [
            'money'=>$this->faker->numberBetween(0,9999)/100,
            'breakfast'=>$this->faker->numberBetween(0,255),
            'lunch'=>$this->faker->numberBetween(0,255),
            'dinner'=>$this->faker->numberBetween(0,255),
        ];
    }
}

<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Academic>
 */
class AcademicFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition()
    {
        return [
            'academic_id'=>$this->faker->unique()->creditCardNumber,
            'a_m'=>$this->faker->unique()->numberBetween('1000000','9999999'),
            'active'=>$this->faker->boolean,
        ];
    }
}

<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\CardApplicant>
 */
class CardApplicantFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition()
    {
        return [
            'department' => $this->faker->company(),
            'first_year' => $this->faker->year(),
            'expiration_date' => $this->faker->dateTimeBetween('-2 years','+5 years'),
            'permanent_address' => $this->faker->address(),
            'permanent_address_phone' => $this->faker->e164PhoneNumber(),
            'temporary_address' => $this->faker->streetAddress(),
            'temporary_address_phone' => $this->faker->e164PhoneNumber(),
            'cellphone' => $this->faker->e164PhoneNumber(),
        ];
    }
}

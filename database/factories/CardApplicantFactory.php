<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use JetBrains\PhpStorm\ArrayShape;

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
    #[ArrayShape(['department' => "string", 'first_year' => "string", 'cellphone' => "int"])]
    public function definition(): array
    {
        return [
            'department' => $this->faker->company(),
            'first_year' => $this->faker->dateTimeBetween('-10 years', 'now')->format('Y'),
            'cellphone' => (int)$this->faker->e164PhoneNumber(),
        ];
    }
}

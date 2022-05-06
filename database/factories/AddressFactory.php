<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use JetBrains\PhpStorm\ArrayShape;

/**
 * @extends Factory
 */
class AddressFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    #[ArrayShape(['is_permanent' => "bool", 'location' => "string", 'phone' => "string"])]
    public function definition(): array
    {
        $is_permanent = $this->faker->boolean;
        return [
            'is_permanent' => $is_permanent,
            'location' => ($is_permanent) ? $this->faker->address() : $this->faker->streetAddress(),
            'phone' => $this->faker->e164PhoneNumber(),
        ];
    }
}

<?php

namespace Database\Factories;

use App\Providers\PatrasAddressProvider;
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
        $this->faker->addProvider(new PatrasAddressProvider($this->faker));
        $is_permanent = $this->faker->boolean;
        return [
            'is_permanent' => $is_permanent,
            'location' => ($is_permanent) ? $this->faker->address() : $this->faker->patrasAddress($this->faker->buildingNumber()),
            'phone' => $this->faker->e164PhoneNumber(),
        ];
    }

    public function permanent(): Factory
    {
        return $this->state(function (array $attributes) {
            return [
                'is_permanent' => true,
                'location' => $this->faker->address(),
            ];
        });
    }

    public function notPermanent(): Factory
    {
        return $this->state(function (array $attributes) {
            return [
                'is_permanent' => false,
                'location' => $this->faker->patrasAddress($this->faker->buildingNumber()),
                'phone' => $this->faker->numerify($this->faker->boolean() ? '+30261#######' : '+3069########'),
            ];
        });
    }
}

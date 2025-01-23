<?php

namespace Database\Factories;

use App\Enum\UserStatusEnum;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory
 */
class UserFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition()
    {
        $created_at = $this->faker->dateTimeBetween('-7 years', 'now');
        return [
            'name' => $this->faker->name(),
            'father_name' => $this->faker->firstName('male'),
            'email' => $this->faker->unique()->safeEmail(),
//            'email_verified_at' => now(),
            'status' => UserStatusEnum::random(),
            'password' => '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', // password
//            'remember_token' => Str::random(10),
            'created_at' => $created_at,
            'updated_at' => $created_at,
        ];
    }

    /**
     * Indicate that the model's email address should be unverified.
     *
     * @return static
     */
    public function unverified()
    {
        return $this->state(function (array $attributes) {
            return [
                'email_verified_at' => null,
            ];
        });
    }
}

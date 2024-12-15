<?php

namespace Database\Factories;

use App\Enum\UserStatusEnum;
use App\Models\Academic;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Academic>
 */
class AcademicFactory extends UserFactory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition()
    {
        return [
            ...parent::definition(),
            'academic_id' => $this->faker->unique()->creditCardNumber,
            'status' => UserStatusEnum::UNDERGRADUATE,
            'a_m' => $this->faker->unique()->numberBetween('1000000', '9999999'),
            'is_active' => $this->faker->boolean
        ];
    }
}

<?php

namespace Database\Factories;

use App\Enum\CardStatusEnum;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory
 */
class CardApplicationFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition()
    {
        return [
            'student_comments' => 'student_comments',
            'card_application_staff_comments' => 'card_application_staff_comments',
            'status' => CardStatusEnum::values()->random(),
        ];
    }
}

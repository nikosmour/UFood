<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\CardApplication>
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
            'status' => array_rand(config('constants.card.application.status')),
        ];
    }
}

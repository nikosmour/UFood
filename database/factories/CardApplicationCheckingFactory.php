<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use JetBrains\PhpStorm\ArrayShape;

/**
 * @extends Factory
 */
class CardApplicationCheckingFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    #[ArrayShape(['card_application_staff_comment' => "string"])]
    public function definition(): array
    {
        return [
            'card_application_staff_comment' => 'card_application_staff_comment',
        ];
    }
}

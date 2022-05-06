<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use JetBrains\PhpStorm\ArrayShape;

/**
 * @extends Factory
 */
class HasCardApplicantCommentFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    #[ArrayShape(['card_applicant_comment' => "string"])]
    public function definition(): array
    {
        return [
            'card_applicant_comment' => 'card_applicants_comment',
        ];
    }
}

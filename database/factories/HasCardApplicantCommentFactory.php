<?php

namespace Database\Factories;

use App\Enum\CardStatusEnum;
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
    #[ArrayShape(['comment' => "string", 'status' => "mixed"])]
    public function definition(): array
    {
        return [
            'comment' => 'Σχόλιο φοιτητή',
            'status' => CardStatusEnum::SUBMITTED
        ];
    }

    public function randomStatus(): Factory
    {
        return $this->state(function (array $attributes) {
            return [
                'status' => collect([CardStatusEnum::SUBMITTED, CardStatusEnum::TEMPORARY_SAVED])->random()
            ];
        });
    }
}

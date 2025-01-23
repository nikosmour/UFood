<?php

namespace Database\Factories;

use App\Enum\CardStatusEnum;
use App\Models\CardApplicationDocument;
use App\Models\HasCardApplicantComment;
use Illuminate\Database\Eloquent\Factories\Factory;
use JetBrains\PhpStorm\ArrayShape;

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
    #[ArrayShape(['expiration_date' => "\DateTime"])]
    public function definition(): array
    {
        return [
            'expiration_date' => $this->faker->dateTimeBetween('-1 years', '+1 years'),
        ];
    }

    public function withComment($isSubmitted = true): factory
    {
        $status = $isSubmitted ? CardStatusEnum::SUBMITTED : CardStatusEnum::TEMPORARY_SAVED;
        return $this->has(HasCardApplicantComment::factory(1, ['status' => $status])->count(1), 'cardApplicantUpdates');
    }

    public function withDocs($wrongId = false): factory
    {
        $docs = [
            'Taxis',
            'AcademicId',
            'deathCertificate'
        ];
        $temp = $this->has(CardApplicationDocument::factory(1)->withCustomValues('NationalId', $wrongId));
        foreach ($docs as $doc) {
            $temp = $temp->has(CardApplicationDocument::factory(1)->withCustomValues($doc));
        }
        return $temp;
    }
}

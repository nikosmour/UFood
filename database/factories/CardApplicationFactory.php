<?php

namespace Database\Factories;

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

    public function withComment(): factory
    {
        return $this->has(HasCardApplicantComment::factory()->randomStatus()->count(1), 'cardApplicantUpdates');
    }

    public function withDocs(): factory
    {
        $docs = [
            'NationalId',
            'Taxis',
            'AcademicId',
            'deathCertificate'
        ];
        $temp = $this;
        foreach ($docs as $doc) {
            $temp = $temp->has(CardApplicationDocument::factory()->withCustomValues($doc)->count(1));
        }
        return $temp;
    }
}

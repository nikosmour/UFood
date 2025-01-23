<?php

namespace Database\Factories;

use App\Models\CardApplicationDocument;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<CardApplicationDocument>
 */
class CardApplicationDocumentFactory extends Factory
{
    private $docs = [
        'NationalId',
        'Taxis',
        'AcademicId'
    ];
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition()
    {
        $doc = $this->docs[array_rand($this->docs)];
        return $this->getValues($doc);
    }

    private function getValues(string $doc, bool $wrong = false): array
    {
        $name = in_array($doc, $this->docs) ? $doc : 'otherInformation';
        return [
            'file_name' => '_fake_' . $name . ($wrong ? '_wrong' : ''),
            'description' => $doc
        ];
    }

    public function withCustomValues($type, $wrong = false): Factory
    {
        return $this->state($this->getValues($type, $wrong));
    }

}

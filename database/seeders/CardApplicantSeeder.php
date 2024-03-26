<?php

namespace Database\Seeders;

use App\Models\Address as Address;
use App\Models\CardApplication;
use App\Models\CardApplicationDocument;
use App\Models\HasCardApplicantComment;
use Database\Seeders\Classes\CreatedAtMoreThanSeeder;
use Illuminate\Database\Seeder;

class CardApplicantSeeder extends CreatedAtMoreThanSeeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $cardApplicants = \App\Models\CardApplicant::whereDoesntHave('cardApplications')->where('created_at', '>', $this->createdAtMoreThan)->cursor();
        foreach ($cardApplicants as $cardApplicant) {
            Address::factory()->permanent()->for($cardApplicant)->create();
            Address::factory()->notPermanent()->for($cardApplicant)->create();
            $cardApplication = CardApplication::factory()->for($cardApplicant)->has(CardApplicationDocument::factory()->count(3))->has(HasCardApplicantComment::factory()->count(1), 'applicantComments')->create();
            $cardApplicationDoc = $cardApplication->cardApplicationDocument[random_int(0, 2)];
            if ($cardApplication->status == \App\Enum\CardStatusEnum::REJECTED) {
                $cardApplicationDoc->status = \App\Enum\CardDocumentStatusEnum::REJECTED;
                $cardApplicationDoc->save();
            } elseif ($cardApplication->status == \App\Enum\CardStatusEnum::INCOMPLETE) {
                $cardApplicationDoc->status = \App\Enum\CardDocumentStatusEnum::INCOMPLETE;
                $cardApplicationDoc->save();
            } elseif ($cardApplication->status == \App\Enum\CardStatusEnum::TEMPORARY_CHECKED) {
                $cardApplicationDoc->status = \App\Enum\CardDocumentStatusEnum::ACCEPTED;
                $cardApplicationDoc->save();
            } elseif ($cardApplication->status == \App\Enum\CardStatusEnum::ACCEPTED) {
                CardApplicationDocument::whereCardApplicationId($cardApplication->id)->update(['status' => \App\Enum\CardDocumentStatusEnum::ACCEPTED]);
            }

        }
    }
}

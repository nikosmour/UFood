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
            CardApplication::factory()->for($cardApplicant)->has(CardApplicationDocument::factory()->count(3))->has(HasCardApplicantComment::factory()->randomStatus()->count(1), 'applicantComments')->create();

        }
    }
}

<?php

namespace Database\Seeders;

use App\Enum\CardStatusEnum;
use App\Models\CardApplicationDocument;
use Database\Seeders\Classes\CreatedAtMoreThanSeeder;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;

class CardApplicationCheckingSeeder extends CreatedAtMoreThanSeeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $cardApplications = \App\Models\CardApplication::whereDoesntHave('staffComments')->where('created_at', '>', $this->createdAtMoreThan)->cursor();
        $cardApplicationStaffs = \App\Models\CardApplicationStaff::all();
        foreach ($cardApplications as $application) {
            $status = \App\Enum\CardStatusEnum::random();
            $applicationDoc = $application->cardApplicationDocument()->first();
            if ($status == \App\Enum\CardStatusEnum::REJECTED)
                $applicationDoc->status = \App\Enum\CardDocumentStatusEnum::REJECTED;
            elseif ($status == \App\Enum\CardStatusEnum::INCOMPLETE)
                $applicationDoc->status = \App\Enum\CardDocumentStatusEnum::INCOMPLETE;
            elseif ($status == \App\Enum\CardStatusEnum::TEMPORARY_CHECKED)
                $applicationDoc->status = \App\Enum\CardDocumentStatusEnum::ACCEPTED;
            elseif ($status == \App\Enum\CardStatusEnum::ACCEPTED)
                CardApplicationDocument::whereCardApplicationId($application->id)->update(['status' => \App\Enum\CardDocumentStatusEnum::ACCEPTED]);

            if (!in_array($status, [CardStatusEnum::SUBMITTED, CardStatusEnum::TEMPORARY_SAVED])) {
                \App\Models\CardApplicationChecking::factory()->for(
                    $application)->for($cardApplicationStaffs->random())->create(['status' => $status]);
                $application->touch();
                $applicationDoc->save();
            }
        }
    }
}

<?php

namespace Database\Seeders;

use App\Enum\CardDocumentStatusEnum;
use App\Enum\CardStatusEnum;
use App\Models\CardApplication;
use App\Models\CardApplicationChecking;
use App\Models\CardApplicationStaff;
use Database\Seeders\Classes\CreatedAtMoreThanSeeder;

class CardApplicationCheckingSeeder extends CreatedAtMoreThanSeeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $cardApplications = CardApplication::whereDoesntHave('cardStaffsUpdates')->cursor();
        $cardApplicationStaffs = CardApplicationStaff::all();
        foreach ($cardApplications as $application) {
            $applicationDoc = $application->cardApplicationDocument()->where('description', 'NationalId')->first();
            $updateData = [];
            $userDigit = $application->academic_id % 8;
            if ($userDigit === 4) {
                $updateData['status'] = CardStatusEnum::REJECTED;
                $updateData['comment'] = 'Η αίτηση σας δεν πληρεί τις προϋποθέσεις εγκρίσεις';
                $applicationDoc->status = CardDocumentStatusEnum::REJECTED;
            } elseif ($userDigit === 3) {
                $updateData['status'] = CardStatusEnum::INCOMPLETE;
                $applicationDoc->status = CardDocumentStatusEnum::INCOMPLETE;
                $applicationDoc->file_name = '_fake_' . 'NationalId_wrong';
                $updateData['comment'] = 'Χρειάζεστε να μας στείλετε και την μπροστά πλευρά της ταυτότητας σας';
            } elseif ($userDigit === 1) {
                $updateData['status'] = CardStatusEnum::CHECKING;
                $applicationDoc->file_name = '_fake_' . 'NationalId_wrong';
                $applicationDoc->status = CardDocumentStatusEnum::INCOMPLETE;
            } elseif ($userDigit === 2) {
                $updateData['status'] = CardStatusEnum::ACCEPTED;
                $applicationDoc->status = CardDocumentStatusEnum::ACCEPTED;
                $application->expiration_date = now()->addYear();
            }
            if (isset($updateData['status'])) {
                CardApplicationChecking::factory()->for(
                    $application)->for($cardApplicationStaffs->random())->create($updateData);
                $application->touch();
                $applicationDoc->save();
            }
        }
    }
}

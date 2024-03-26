<?php

namespace Database\Seeders;

use App\Enum\CardStatusEnum;
use App\Models\CardApplicationDocument;
use Database\Seeders\Classes\CreatedAtMoreThanSeeder;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

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
            if (!in_array($application->status, [CardStatusEnum::SUBMITTED, CardStatusEnum::TEMPORARY_SAVED])) {
                \App\Models\CardApplicationChecking::factory()->for(
                    $application)->for($cardApplicationStaffs->random())->create();
                $application->touch();

            }
        }
    }
}

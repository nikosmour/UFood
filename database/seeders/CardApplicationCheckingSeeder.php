<?php

namespace Database\Seeders;

use App\Enum\CardStatusEnum;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class CardApplicationCheckingSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $cardApplications = \App\Models\CardApplication::all();
        $cardApplicationStaffs = \App\Models\CardApplicationStaff::all();
        foreach ($cardApplications as $application) {
            if (!in_array($application->status, [CardStatusEnum::SUBMITTED, CardStatusEnum::TEMPORARY_SAVED]))
                \App\Models\CardApplicationChecking::factory()->for(
                    $application)->for($cardApplicationStaffs->random())->create();
        }
    }
}

<?php

namespace Database\Seeders;

use App\Models\CardApplicant;
use App\Models\EntryStaff;
use App\Models\UsageCard;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\QueryException;
use Illuminate\Database\Seeder;


class UsageCardSeeder extends Seeder
{
    use WithoutModelEvents;

    public function __construct(protected string $createdAtMoreThan = '1900-01-01 12:00:00')
    {
    }
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        /** @var CardApplicant[] $cardApplicants */
        $cardApplicants = CardApplicant::withOnly([])->where('created_at', '>', $this->createdAtMoreThan)->cursor();
        foreach ($cardApplicants as $applicant) {
            try {
                $staff = EntryStaff::inRandomOrder()->first();
                UsageCard::factory()->for($staff)->for($applicant)->create();
            } catch (QueryException  $e) {
                if ($e->getCode() != 23000)
                    throw $e;
            }
        }
    }
}

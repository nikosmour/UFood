<?php

namespace Database\Seeders;

use App\Models\CardApplicant;
use App\Models\EntryStaff;
use Illuminate\Database\QueryException;
use Illuminate\Database\Seeder;

class UsageCardSeeder extends Seeder
{

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
        $cardApplicants = CardApplicant::where('created_at', '>', $this->createdAtMoreThan)->cursor();
        foreach ($cardApplicants as $applicant) {
            /** type {CardApplicant} $applicant */
            try {
                $applicant->usageCard()->create([
                    'entry_staff_id' => EntryStaff::inRandomOrder()->select(['id'])->first()->id
                ]);
            } catch (QueryException  $e) {
                if ($e->getCode() != 23000)
                    throw $e;
            }
        }
    }
}

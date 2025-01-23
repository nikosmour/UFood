<?php

namespace Database\Seeders;

use App\Models\Address as Address;
use App\Models\CardApplicant;
use App\Models\CardApplication;
use App\Models\Department;
use Database\Seeders\Classes\CreatedAtMoreThanSeeder;

class CardApplicantSeeder extends CreatedAtMoreThanSeeder
{
    private bool $extra;
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function __construct($createdAtMoreThan = '1900-01-01 12:00:00', $extra = false)
    {
        parent::__construct($createdAtMoreThan);
        $this->extra = $extra;
    }
    public function run()
    {
        $cardApplicants = CardApplicant::whereDoesntHave('addresses')->cursor();
        $departments = Department::all();
        foreach ($cardApplicants as $cardApplicant) {
            Address::factory()->permanent()->for($cardApplicant)->create();
            Address::factory()->notPermanent()->for($cardApplicant)->create();
            $cardApplicant->department_id = $departments->random()->id;
            $cardApplicant->save();
            if ($this->extra) CardApplication::factory()->for($cardApplicant)->withComment(!in_array($cardApplicant->academic_id % 8, [
                5,
                6
            ]))->withDocs($cardApplicant->academic_id % 2 === 1)->create();
        }
    }
}

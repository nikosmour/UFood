<?php

namespace Database\Seeders;

use Database\Seeders\Classes\ManyToManySeeder;

class UsageCardSeeder extends ManyToManySeeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $this->make_connection(
            \App\Models\CardApplicant::where('created_at', '>', $this->createdAtMoreThan)->cursor(),
            \App\Models\EntryStaff::all(),
            \App\Models\UsageCard::class, $this->count);
    }
}

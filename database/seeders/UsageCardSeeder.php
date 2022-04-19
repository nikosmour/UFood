<?php

namespace Database\Seeders;

use Database\Seeders\Classes\ManyToManySeeder;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;

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
            \App\Models\CardApplicant::all(),
            \App\Models\EntryStaff::all(),
            \App\Models\UsageCard::class,5);
    }
}

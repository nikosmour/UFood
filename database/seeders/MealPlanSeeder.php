<?php

namespace Database\Seeders;

use App\Models\MealPlan;
use Carbon\CarbonPeriod;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Eloquent\Factories\Sequence;
use Illuminate\Database\Seeder;
class MealPlanSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $from = now();
        $last =MealPlan::all()->last();
        if($last)
            $from= $last->date->addDay();
        $to= (clone $from)->addDays(10);
        $date_range = CarbonPeriod::create($from ,'1 day', $to)->toArray();
        foreach ($date_range as $date)
            foreach (config('constants.meal.plan.period') as $period)
                MealPlan::factory()->create(['date' => $date, 'period' => $period]);
    }
}

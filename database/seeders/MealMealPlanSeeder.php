<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class MealMealPlanSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        if(\App\Models\Meal::all()->count()===0)
            $this->call(MealSeeder::class);
        if(\App\Models\MealPlan::all()->count()===0)
            $this->call(MealPlanSeeder::class);
        $meals=\App\Models\Meal::all();
        $mealPlans=\App\Models\MealPlan::all();
        foreach ( $mealPlans as $mealPlan){
            $meals=$meals->shuffle();
            $mealPlan->meal()->savemany($meals->slice(0,10));
        }

    }
}

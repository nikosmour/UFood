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
        $last=null;
        if(\App\Models\Meal::all()->count()===0)
            $this->call(MealSeeder::class);
        if(\App\Models\MealPlan::all()->count()<=30) {
            $last=\App\Models\MealPlan::all('id')->last();
            $this->call(MealPlanSeeder::class);
        }
        if(is_null($last))
            $last_id=0;
        else
            $last_id=$last->id;
        $meals=\App\Models\Meal::all();
        $mealPlans=\App\Models\MealPlan::where('id','>',$last_id)->get();
        foreach ( $mealPlans as $mealPlan){
            $meals=$meals->shuffle();
            $mealPlan->meal()->savemany($meals->slice(0,10));
        }
    }
}

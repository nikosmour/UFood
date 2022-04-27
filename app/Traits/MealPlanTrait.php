<?php


namespace App\Traits;

trait MealPlanTrait
{
    /**
     * Find the  current  food period
     *
     * @return String
     */
    private function getCurrentMealPeriod(): string
    {
        $hours=(int)date('H');
        if ($hours< 12)
            return 'breakfast';
        elseif ($hours< 18)
            return 'lunch';
        else
            return 'dinner';
    }

}

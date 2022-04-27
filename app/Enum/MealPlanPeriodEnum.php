<?php

namespace App\Enum;

use App\Traits\EnumToArray;

enum MealPlanPeriodEnum: string
{
    use EnumToArray;

    case BREAKFAST = 'breakfast';
    case LUNCH = 'lunch';
    case DINNER = 'dinner';
}

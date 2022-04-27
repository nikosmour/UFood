<?php

namespace App\Enum;

use App\Traits\EnumToArray;

enum MealCategoryEnum: string
{
    use EnumToArray;

    case BREAKFAST = 'breakfast';
    case MEAL = 'meal';
    case SALAD = 'salad';
    case EXTRA = 'extra';
}

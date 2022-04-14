<?php
return [
    /*
   |--------------------------------------------------------------------------
   | Meal Category
   |--------------------------------------------------------------------------
   |
   | Here is written all the categories that can be a meal.That is used in the
   | migration of the meals table. So if you have already made the migration
   | it is important to change also the values in the database.
   |
   */
    'category'=>[
        'breakfast',
        'meal',
        'salad',
        'extra',
    ],
    'plan'=>[
        'period'=> [
            'breakfast',
            'lunch',
            'dinner']
    ]
];

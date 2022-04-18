<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Meal>
 */
class MealFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition()
    {
        $category=config('constants.meal.category');
        return [
            'category' => $category[array_rand($category)],
            'description' => 'fake'. now()->toDateTimeString(),
        ];
    }
}

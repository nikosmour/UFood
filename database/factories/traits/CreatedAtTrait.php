<?php


namespace Database\Factories\traits;

use Illuminate\Database\Eloquent\Factories\Factory;

trait CreatedAtTrait
{

    public function createdAt($min = null, $max = null): Factory
    {
        return $this->state(['created_at' => $this->faker->dateTimeBetween($min ?? now(), $max ?? now())]);
    }
}

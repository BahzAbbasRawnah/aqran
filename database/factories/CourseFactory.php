<?php

namespace Database\Factories;

use App\Models\Course;
use App\Models\Major;
use Illuminate\Database\Eloquent\Factories\Factory;

class CourseFactory extends Factory
{
    protected $model = Course::class;

    public function definition(): array
    {
        return [
            'major_id' => Major::factory(),
            'code' => strtoupper(fake()->bothify('???-###')),
            'name' => fake()->words(3, true),
        ];
    }
}

<?php

namespace Database\Factories;

use App\Models\Student;
use App\Models\Task;
use Illuminate\Database\Eloquent\Factories\Factory;

class TaskFactory extends Factory
{
    protected $model = Task::class;

    public function definition(): array
    {
        return [
            'student_id' => Student::factory(),
            'title' => fake()->sentence(),
            'description' => fake()->paragraph(),
            'due_datetime' => fake()->dateTimeBetween('-1 month', '+1 month'),
            'is_completed' => fake()->boolean(),
        ];
    }
}

<?php

namespace Database\Factories;

use App\Models\Major;
use App\Models\Student;
use App\Models\StudyPlan;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

class StudentFactory extends Factory
{
    protected $model = Student::class;

    public function definition(): array
    {
        return [
            'user_id' => User::factory(),
            'major_id' => Major::factory(),
            'study_plan_id' => StudyPlan::factory(),
            'enrollment_year' => fake()->numberBetween(2020, 2024),
        ];
    }
}

<?php

namespace Database\Factories;

use App\Models\Major;
use App\Models\StudyPlan;
use Illuminate\Database\Eloquent\Factories\Factory;

class StudyPlanFactory extends Factory
{
    protected $model = StudyPlan::class;

    public function definition(): array
    {
        return [
            'major_id' => Major::factory(),
            'name' => 'Plan ' . fake()->year(),
            'effective_year' => fake()->numberBetween(2020, 2024),
        ];
    }
}

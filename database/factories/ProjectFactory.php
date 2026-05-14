<?php

namespace Database\Factories;

use App\Models\Project;
use Illuminate\Database\Eloquent\Factories\Factory;

class ProjectFactory extends Factory
{
    protected $model = Project::class;

    public function definition(): array
    {
        return [
            'name' => fake()->unique()->words(3, true),
            'description' => fake()->paragraph(),
            'chat_link' => fake()->url(),
            'semester_end_date' => $endDate = fake()->dateTimeBetween('+1 month', '+4 months'),
            'expires_at' => (clone $endDate)->modify('+7 days'),
        ];
    }
}

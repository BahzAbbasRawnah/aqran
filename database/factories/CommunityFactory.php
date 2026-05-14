<?php

namespace Database\Factories;

use App\Models\Community;
use Illuminate\Database\Eloquent\Factories\Factory;

class CommunityFactory extends Factory
{
    protected $model = Community::class;

    public function definition(): array
    {
        return [
            'name' => fake()->unique()->word() . ' Community',
            'description' => fake()->paragraph(),
            'category' => fake()->randomElement(['Engineering', 'Medicine', 'Art', 'Business']),
            'join_link' => fake()->url(),
            'cover_image' => 'https://via.placeholder.com/640x480.png?text=Community',
            'member_count' => fake()->numberBetween(10, 1000),
        ];
    }
}

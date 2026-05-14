<?php
 
namespace Database\Factories;
 
use App\Models\Community;
use App\Models\Major;
use App\Models\User;
use App\Models\Workshop;
use Illuminate\Database\Eloquent\Factories\Factory;
 
class WorkshopFactory extends Factory
{
    protected $model = Workshop::class;
 
    public function definition(): array
    {
        return [
            'user_id' => User::factory(),
            'community_id' => Community::factory(),
            'title' => fake()->sentence(),
            'description' => fake()->text(500),
            'video_url' => 'https://www.youtube.com/watch?v=' . fake()->lexify('???????????'),
            'thumbnail_url' => 'https://via.placeholder.com/640x480.png?text=Workshop',
            'target_audience_major_id' => fn() => Major::inRandomOrder()->first()?->id ?? Major::factory(),
            'status' => fake()->randomElement(['pending', 'approved', 'rejected']),
            'reject_reason' => function (array $attributes) {
                return $attributes['status'] === 'rejected' ? fake()->sentence() : null;
            },
        ];
    }
}

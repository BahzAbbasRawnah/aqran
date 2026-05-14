<?php
 
namespace Database\Factories;
 
use App\Models\Announcement;
use App\Models\Community;
use Illuminate\Database\Eloquent\Factories\Factory;
 
class AnnouncementFactory extends Factory
{
    protected $model = Announcement::class;
 
    public function definition(): array
    {
        return [
            'community_id' => Community::factory(),
            'title' => fake()->sentence(),
            'content' => fake()->paragraph(),
            'image_url' => 'https://via.placeholder.com/640x480.png?text=Announcement',
            'publish_date' => now(),
            'expires_at' => now()->addDays(7),
            'status' => fake()->randomElement(['pending', 'approved', 'rejected']),
            'reject_reason' => function (array $attributes) {
                return $attributes['status'] === 'rejected' ? fake()->sentence() : null;
            },
        ];
    }
}

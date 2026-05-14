<?php
 
namespace Database\Factories;
 
use App\Models\Project;
use App\Models\ProjectFile;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;
 
class ProjectFileFactory extends Factory
{
    protected $model = ProjectFile::class;
 
    public function definition(): array
    {
        return [
            'project_id' => Project::factory(),
            'user_id' => User::factory(),
            'is_pinned' => fake()->boolean(20),
            'file_path' => 'files/' . fake()->uuid() . '.pdf',
            'file_name' => fake()->word() . '.pdf',
            'file_size_mb' => fake()->randomFloat(2, 0.1, 50),
            'description' => fake()->sentence(),
        ];
    }
}

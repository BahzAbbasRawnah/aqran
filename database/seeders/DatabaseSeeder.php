<?php

namespace Database\Seeders;

use App\Models\Community;
use App\Models\Course;
use App\Models\Major;
use App\Models\Project;
use App\Models\Student;
use App\Models\StudyPlan;
use App\Models\Task;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        $this->call([
            UserSeeder::class,
            MajorSeeder::class,
            CourseSeeder::class,
            StudyPlanSeeder::class,
            StudentSeeder::class,
        ]);
    }
}

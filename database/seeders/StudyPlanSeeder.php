<?php

namespace Database\Seeders;

use App\Models\Course;
use App\Models\Major;
use App\Models\StudyPlan;
use Illuminate\Database\Seeder;

class StudyPlanSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $csMajor = Major::query()->where('name', 'علوم الحاسب')->first();

        if (!$csMajor) return;

        $studyPlan = StudyPlan::create([
            'major_id' => $csMajor->id,
            'name' => 'خطة علوم الحاسب 2024',
            'effective_year' => 2024,
        ]);

        $courses = Course::query()->where('major_id', $csMajor->id)->get()->keyBy('code');

        // Map courses to semester levels
        $planCourses = [
            ['code' => 'CCCS 111', 'level' => 1, 'type' => 'mandatory'],
            ['code' => 'ARAB 101', 'level' => 1, 'type' => 'mandatory'],
            ['code' => 'CCCS 121', 'level' => 2, 'type' => 'mandatory'],
            ['code' => 'STAT 101', 'level' => 2, 'type' => 'mandatory'],
            ['code' => 'CPCS 202', 'level' => 3, 'type' => 'mandatory'], // Data Structures
            ['code' => 'MATH 202', 'level' => 3, 'type' => 'mandatory'], // Calculus 2
            ['code' => 'CCCS 417', 'level' => 5, 'type' => 'mandatory'], // Compilers
            ['code' => 'CCCS 432', 'level' => 7, 'type' => 'mandatory'], // Distributed Parallel Computing
        ];

        foreach ($planCourses as $pc) {
            if ($courses->has($pc['code'])) {
                $studyPlan->courses()->attach($courses[$pc['code']]->id, [
                    'semester_level' => $pc['level'],
                    'course_type' => $pc['type'],
                ]);
            }
        }
    }
}

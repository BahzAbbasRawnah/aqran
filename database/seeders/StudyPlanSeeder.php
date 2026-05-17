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
        $majorPlans = [
            'علوم الحاسب' => [
                'CS 111' => ['level' => 1, 'type' => 'mandatory'],
                'MATH 101' => ['level' => 1, 'type' => 'mandatory'],
                'PHYS 101' => ['level' => 1, 'type' => 'mandatory'],
                'CS 121' => ['level' => 2, 'type' => 'mandatory'],
                'CS 102' => ['level' => 2, 'type' => 'mandatory'],
                'CS 202' => ['level' => 3, 'type' => 'mandatory'],
                'CS 224' => ['level' => 3, 'type' => 'mandatory'],
                'CS 311' => ['level' => 4, 'type' => 'mandatory'],
                'CS 417' => ['level' => 5, 'type' => 'mandatory'],
                'CS 432' => ['level' => 7, 'type' => 'mandatory'],
            ],
            'هندسة البرمجيات' => [
                'SE 111' => ['level' => 1, 'type' => 'mandatory'],
                'MATH 102' => ['level' => 1, 'type' => 'mandatory'],
                'PHYS 102' => ['level' => 1, 'type' => 'mandatory'],
                'SE 121' => ['level' => 2, 'type' => 'mandatory'],
                'SE 102' => ['level' => 2, 'type' => 'mandatory'],
                'SE 201' => ['level' => 3, 'type' => 'mandatory'],
                'SE 220' => ['level' => 3, 'type' => 'mandatory'],
                'SE 312' => ['level' => 4, 'type' => 'mandatory'],
                'SE 325' => ['level' => 5, 'type' => 'mandatory'],
                'SE 441' => ['level' => 7, 'type' => 'mandatory'],
            ],
            'الذكاء الاصطناعي' => [
                'AI 111' => ['level' => 1, 'type' => 'mandatory'],
                'MATH 103' => ['level' => 1, 'type' => 'mandatory'],
                'PHYS 103' => ['level' => 1, 'type' => 'mandatory'],
                'AI 121' => ['level' => 2, 'type' => 'mandatory'],
                'AI 102' => ['level' => 2, 'type' => 'mandatory'],
                'AI 201' => ['level' => 3, 'type' => 'mandatory'],
                'AI 302' => ['level' => 4, 'type' => 'mandatory'],
                'AI 411' => ['level' => 5, 'type' => 'mandatory'],
                'AI 422' => ['level' => 7, 'type' => 'mandatory'],
                'AI 435' => ['level' => 7, 'type' => 'mandatory'],
            ],
            'الأمن السيبراني' => [
                'CYS 111' => ['level' => 1, 'type' => 'mandatory'],
                'MATH 104' => ['level' => 1, 'type' => 'mandatory'],
                'PHYS 104' => ['level' => 1, 'type' => 'mandatory'],
                'CYS 121' => ['level' => 2, 'type' => 'mandatory'],
                'CYS 102' => ['level' => 2, 'type' => 'mandatory'],
                'CYS 201' => ['level' => 3, 'type' => 'mandatory'],
                'CYS 305' => ['level' => 4, 'type' => 'mandatory'],
                'CYS 412' => ['level' => 5, 'type' => 'mandatory'],
                'CYS 431' => ['level' => 7, 'type' => 'mandatory'],
                'CYS 444' => ['level' => 7, 'type' => 'mandatory'],
            ]
        ];

        foreach ($majorPlans as $majorName => $courseMappings) {
            $major = Major::query()->where('name', $majorName)->first();
            if (!$major) {
                continue;
            }

            // Retrieve all courses belonging to this major
            $courses = Course::query()->where('major_id', $major->id)->get()->keyBy('code');

            // Seed multiple Study Plans for enrollment years (e.g., 2024 and 2026)
            $years = [2024, 2026];
            foreach ($years as $year) {
                $plan = StudyPlan::create([
                    'major_id' => $major->id,
                    'name' => "خطة {$majorName} {$year}",
                    'effective_year' => $year,
                ]);

                // Attach courses to this study plan based on levels
                foreach ($courseMappings as $code => $map) {
                    if ($courses->has($code)) {
                        $plan->courses()->attach($courses[$code]->id, [
                            'semester_level' => $map['level'],
                            'course_type' => $map['type'],
                        ]);
                    }
                }
            }
        }
    }
}

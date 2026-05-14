<?php

namespace Database\Seeders;

use App\Models\User;
use App\Models\Student;
use App\Models\Major;
use App\Models\StudyPlan;
use Illuminate\Database\Seeder;

class StudentSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $csMajor = Major::query()->where('name', 'علوم الحاسب')->first();
        $csPlan = $csMajor ? $csMajor->studyPlans()->where('effective_year', 2024)->first() : null;

        if (!$csMajor || !$csPlan) return;

        $studentUsers = User::query()->where('role', 'student')->get();

        foreach ($studentUsers as $user) {
            Student::firstOrCreate(
                ['user_id' => $user->id],
                [
                    'major_id' => $csMajor->id,
                    'study_plan_id' => $csPlan->id,
                    'enrollment_year' => 2024,
                ]
            );
        }
    }
}

<?php

namespace Database\Seeders;

use App\Models\Course;
use App\Models\Major;
use Illuminate\Database\Seeder;

class CourseSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $csMajor = Major::query()->where('name', 'علوم الحاسب')->first();

        if (!$csMajor) return;

        $courses = [
            ['code' => 'CCCS 432', 'name' => 'الحوسبة المتوازية الموزعة'],
            ['code' => 'CCCS 417', 'name' => 'بناء المترجمات'],
            ['code' => 'CCCS 111', 'name' => 'مقدمة برمجة'],
            ['code' => 'CCCS 121', 'name' => 'البرمجة الشيئية'],
            ['code' => 'CPCS 202', 'name' => 'تراكيب البيانات'],
            ['code' => 'MATH 202', 'name' => 'تفاضل وتكامل ٢'],
            ['code' => 'STAT 101', 'name' => 'إحصاء عام'],
            ['code' => 'ARAB 101', 'name' => 'الكتابة الأكاديمية بالإنجليزية'],
        ];

        foreach ($courses as $course) {
            Course::create(array_merge($course, ['major_id' => $csMajor->id]));
        }
    }
}

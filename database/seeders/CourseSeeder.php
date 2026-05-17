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
        $majorData = [
            'علوم الحاسب' => [
                // General
                ['code' => 'CS 111', 'name' => 'مقدمة في البرمجة'],
                ['code' => 'CS 121', 'name' => 'البرمجة كائنية التوجه (OOP)'],
                ['code' => 'MATH 101', 'name' => 'حساب التفاضل والتكامل'],
                ['code' => 'CS 102', 'name' => 'التراكيب المتقطعة'],
                ['code' => 'PHYS 101', 'name' => 'الفيزياء العامة'],
                // Specialized
                ['code' => 'CS 202', 'name' => 'تراكيب البيانات'],
                ['code' => 'CS 311', 'name' => 'نظم التشغيل'],
                ['code' => 'CS 417', 'name' => 'هندسة المترجمات'],
                ['code' => 'CS 432', 'name' => 'الحوسبة المتوازية والموزعة'],
                ['code' => 'CS 224', 'name' => 'تنظيم بنيان الحاسوب'],
            ],
            'هندسة البرمجيات' => [
                // General
                ['code' => 'SE 111', 'name' => 'مقدمة في البرمجة'],
                ['code' => 'SE 121', 'name' => 'البرمجة كائنية التوجه (OOP)'],
                ['code' => 'MATH 102', 'name' => 'حساب التفاضل والتكامل'],
                ['code' => 'SE 102', 'name' => 'التراكيب المتقطعة'],
                ['code' => 'PHYS 102', 'name' => 'الفيزياء العامة'],
                // Specialized
                ['code' => 'SE 201', 'name' => 'هندسة المتطلبات'],
                ['code' => 'SE 312', 'name' => 'بنيان وتصميم البرمجيات'],
                ['code' => 'SE 325', 'name' => 'جودة واختبار البرمجيات'],
                ['code' => 'SE 441', 'name' => 'إدارة مشاريع البرمجيات'],
                ['code' => 'SE 220', 'name' => 'منهجيات التطوير الرشيقة (Agile)'],
            ],
            'الذكاء الاصطناعي' => [
                // General
                ['code' => 'AI 111', 'name' => 'مقدمة في البرمجة'],
                ['code' => 'AI 121', 'name' => 'البرمجة كائنية التوجه (OOP)'],
                ['code' => 'MATH 103', 'name' => 'حساب التفاضل والتكامل'],
                ['code' => 'AI 102', 'name' => 'التراكيب المتقطعة'],
                ['code' => 'PHYS 103', 'name' => 'الفيزياء العامة'],
                // Specialized
                ['code' => 'AI 201', 'name' => 'مقدمة في الذكاء الاصطناعي'],
                ['code' => 'AI 302', 'name' => 'تعلم الآلة (Machine Learning)'],
                ['code' => 'AI 411', 'name' => 'الشبكات العصبية والتعلم العميق'],
                ['code' => 'AI 422', 'name' => 'الرؤية الحاسوبية'],
                ['code' => 'AI 435', 'name' => 'معالجة اللغات الطبيعية'],
            ],
            'الأمن السيبراني' => [
                // General
                ['code' => 'CYS 111', 'name' => 'مقدمة في البرمجة'],
                ['code' => 'CYS 121', 'name' => 'البرمجة كائنية التوجه (OOP)'],
                ['code' => 'MATH 104', 'name' => 'حساب التفاضل والتكامل'],
                ['code' => 'CYS 102', 'name' => 'التراكيب المتقطعة'],
                ['code' => 'PHYS 104', 'name' => 'الفيزياء العامة'],
                // Specialized
                ['code' => 'CYS 201', 'name' => 'أمن الشبكات'],
                ['code' => 'CYS 305', 'name' => 'التشفير وأمن المعلومات'],
                ['code' => 'CYS 412', 'name' => 'التحقيق الجنائي الرقمي'],
                ['code' => 'CYS 431', 'name' => 'الاختراق الأخلاقي'],
                ['code' => 'CYS 444', 'name' => 'إدارة مخاطر الحوكمة والامتثال'],
            ]
        ];

        foreach ($majorData as $majorName => $courses) {
            $major = Major::query()->where('name', $majorName)->first();
            if ($major) {
                foreach ($courses as $course) {
                    Course::create(array_merge($course, ['major_id' => $major->id]));
                }
            }
        }
    }
}

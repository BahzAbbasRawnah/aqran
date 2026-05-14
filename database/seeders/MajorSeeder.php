<?php

namespace Database\Seeders;

use App\Models\Major;
use Illuminate\Database\Seeder;

class MajorSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $majors = [
            ['name' => 'علوم الحاسب'],
            ['name' => 'هندسة البرمجيات'],
            ['name' => 'الذكاء الاصطناعي'],
            ['name' => 'الأمن السيبراني'],
        ];

        foreach ($majors as $major) {
            Major::create($major);
        }
    }
}

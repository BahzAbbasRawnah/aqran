<?php

namespace Database\Seeders;

use App\Models\User;
use App\Models\Student;
use App\Models\Major;
use App\Models\StudyPlan;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class UserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Admin
        User::create([
            'username' => 'admin',
            'first_name' => 'مدير',
            'last_name' => 'النظام',
            'email' => 'admin@aqran.sa',
            'password' => Hash::make('password'),
            'role' => 'admin',
        ]);

        // Students
        $studentsData = [
            ['name' => 'عبدالمحسن الكعيد', 'email' => 'a.alkaied@student.edu.sa', 'username' => 'alkaied'],
            ['name' => 'بدر الغامدي', 'email' => 'b.alghamdi@student.edu.sa', 'username' => 'alghamdi'],
            ['name' => 'لؤي العتيبي', 'email' => 'l.alotaibi@student.edu.sa', 'username' => 'alotaibi'],
            ['name' => 'عبدالعزيز المالكي', 'email' => 'a.almalki@student.edu.sa', 'username' => 'almalki'],
        ];

        foreach ($studentsData as $data) {
            $names = explode(' ', $data['name']);
            $user = User::create([
                'username' => $data['username'],
                'first_name' => $names[0],
                'last_name' => $names[count($names) - 1],
                'email' => $data['email'],
                'password' => Hash::make('password'),
                'role' => 'student',
            ]);

            // Create student profile (will be linked to major/plan in DatabaseSeeder or here if they exist)
            // For now, just create the user. Student profile creation depends on MajorSeeder and StudyPlanSeeder.
        }
    }
}

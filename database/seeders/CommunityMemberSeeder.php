<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class CommunityMemberSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $users = \App\Models\User::all();
        $communities = \App\Models\Community::all();

        if ($communities->isEmpty()) {
            // Create some realistic communities if none exist
            $communities = collect([
                \App\Models\Community::create([
                    'name' => 'طلاب علوم الحاسب - جامعة جدة',
                    'description' => 'مجتمع خاص بطلاب كلية علوم وهندسة الحاسب لمشاركة الموارد والنقاشات الأكاديمية.',
                    'category' => 'أكاديمي',
                    'join_link' => 'https://chat.whatsapp.com/GzBfE9...',
                    'member_count' => 0,
                ]),
                \App\Models\Community::create([
                    'name' => 'فرص التدريب والتعاوني',
                    'description' => 'مجموعة لمشاركة فرص التدريب الصيفي والتعاوني للطلاب الجامعيين في مجال التقنية.',
                    'category' => 'تدريب وتوظيف',
                    'join_link' => 'https://chat.whatsapp.com/KxP1O...',
                    'member_count' => 0,
                ]),
                \App\Models\Community::create([
                    'name' => 'مشاريع التخرج CS',
                    'description' => 'تبادل الأفكار والخبرات حول مشاريع التخرج في تخصصات علوم الحاسب والذكاء الاصطناعي.',
                    'category' => 'مشاريع',
                    'join_link' => 'https://chat.whatsapp.com/Lp9W2...',
                    'member_count' => 0,
                ]),
            ]);
        } else {
            // Update existing communities with realistic categories if they are null
            foreach ($communities as $index => $community) {
                $categories = ['أكاديمي', 'تدريب وتوظيف', 'مشاريع'];
                $community->update([
                    'category' => $categories[$index % 3],
                    'join_link' => 'https://chat.whatsapp.com/example' . $community->id,
                ]);
            }
        }

        // Attach random members to each community
        foreach ($communities as $community) {
            $randomUsers = $users->random(min(20, $users->count()));
            foreach ($randomUsers as $user) {
                if (!$community->members()->where('user_id', $user->id)->exists()) {
                    $community->members()->attach($user->id, ['joined_at' => now()]);
                    $community->increment('member_count');
                }
            }
        }
    }
}

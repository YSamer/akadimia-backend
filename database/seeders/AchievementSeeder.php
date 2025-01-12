<?php

namespace Database\Seeders;

use App\Models\Achievement;
use Illuminate\Database\Seeder;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;

class AchievementSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $achievements = [
            [
                'name' => 'قراءة الكتاب',
                'description' => 'قراءة كتاب المئة المانحة لإتقانه كالفاتحة .',
                'urls' => [
                    ['title' => 'المئة المانحة لإتقانه كالفاتحة', 'url' => 'uploads/books/100 to etqan.pdf', 'type' => 'pdf']
                ],
                'image' => 'uploads/images/100 to etqan image.png',
                'type' => 'read',
            ],
            [
                'name' => 'سماع السلسة',
                'description' => 'سماع سلسلة الحصون الخمسة للدكتور سعيد حمزة.',
                'urls' => [
                    ['title' => 'هل أنت حافظ حقا؟', 'url' => 'uploads/sounds/hosson khamsa/hosson-01.m4a', 'type' => 'audio'],
                    ['title' => 'لماذا نحفظ القرآن؟', 'url' => 'uploads/sounds/hosson khamsa/hosson-02.m4a', 'type' => 'audio'],
                    ['title' => 'الإخلاص وتحديد الهدف', 'url' => 'uploads/sounds/hosson khamsa/hosson-03.m4a', 'type' => 'audio'],
                    ['title' => 'أركان الحفظ', 'url' => 'uploads/sounds/hosson khamsa/hosson-04.m4a', 'type' => 'audio'],
                    ['title' => 'التكرار', 'url' => 'uploads/sounds/hosson khamsa/hosson-05.m4a', 'type' => 'audio'],
                    ['title' => 'التركيز وعلاج الشرود والسرحان', 'url' => 'uploads/sounds/hosson khamsa/hosson-06.m4a', 'type' => 'audio'],
                    ['title' => 'التحضير', 'url' => 'uploads/sounds/hosson khamsa/hosson-07.m4a', 'type' => 'audio'],
                    ['title' => 'الحفظ الجديد', 'url' => 'uploads/sounds/hosson khamsa/hosson-08.m4a', 'type' => 'audio'],
                    ['title' => 'المراجعة', 'url' => 'uploads/sounds/hosson khamsa/hosson-09.m4a', 'type' => 'audio'],
                    ['title' => 'مراجعة البعيد', 'url' => 'uploads/sounds/hosson khamsa/hosson-10.m4a', 'type' => 'audio'],
                    ['title' => 'أركان المراجعة المثمرة', 'url' => 'uploads/sounds/hosson khamsa/hosson-11.m4a', 'type' => 'audio'],
                    ['title' => 'العناية بالمتشابهات اللفظية', 'url' => 'uploads/sounds/hosson khamsa/hosson-12.m4a', 'type' => 'audio'],
                    ['title' => 'العناية بالمتشابهات اللفظية 2', 'url' => 'uploads/sounds/hosson khamsa/hosson-13.m4a', 'type' => 'audio'],
                    ['title' => 'العوائق', 'url' => 'uploads/sounds/hosson khamsa/hosson-14.m4a', 'type' => 'audio'],
                ],
                'image' => null,
                'type' => 'listen',
            ],
            [
                'name' => 'دفع مصاريف التقديم',
                'description' => 'دفع مصاريف التقديم 200 ج.م.',
                'urls' => [
                    ['title' => 'انستا باي', 'url' => 'ammarsa3d55', 'type' => 'instapay'],
                    ['title' => 'انستا باي', 'url' => '01003625246', 'type' => 'instapay'],
                    ['title' => 'كاش', 'url' => '01143113706', 'type' => 'phone'],
                ],
                'image' => null,
                'type' => 'payment',
            ],
        ];

        foreach ($achievements as $achievementData) {
            $achievement = Achievement::create([
                'name' => $achievementData['name'],
                'description' => $achievementData['description'],
                'image' => $achievementData['image'],
                'type' => $achievementData['type'],
            ]);

            foreach ($achievementData['urls'] as $url) {
                $achievement->urls()->create($url);
            }
        }
    }
}

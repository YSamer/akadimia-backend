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
                'urls' => json_encode(['المئة المانحة لإتقانه كالفاتحة' => 'uploads/books/100 to etqan.pdf']),
                'image' => 'uploads/images/100 to etqan image.png',
                'type' => 'read',
            ],
            [
                'name' => 'سماع السلسة',
                'description' => 'سماع سلسلة الحصون الخمسة للدكتور سعيد حمزة.',
                'urls' => json_encode([
                    'هل أنت حافظ حقا؟' => 'uploads/sounds/hosson khamsa/hosson-01.m4a',
                    'لماذا نحفظ القرآن؟' => 'uploads/sounds/hosson khamsa/hosson-02.m4a',
                    'الإخلاص وتحديد الهدف' => 'uploads/sounds/hosson khamsa/hosson-03.m4a',
                    'أركان الحفظ' => 'uploads/sounds/hosson khamsa/hosson-04.m4a',
                    'التكرار' => 'uploads/sounds/hosson khamsa/hosson-05.m4a',
                    'التركيز وعلاج الشرود والسرحان' => 'uploads/sounds/hosson khamsa/hosson-06.m4a',
                    'التحضير' => 'uploads/sounds/hosson khamsa/hosson-07.m4a',
                    'الحفظ الجديد' => 'uploads/sounds/hosson khamsa/hosson-08.m4a',
                    'المراجعة' => 'uploads/sounds/hosson khamsa/hosson-09.m4a',
                    'مراجعة البعيد' => 'uploads/sounds/hosson khamsa/hosson-10.m4a',
                    'أركان المراجعة المثمرة' => 'uploads/sounds/hosson khamsa/hosson-11.m4a',
                    'العناية بالمتشابهات اللفظية' => 'uploads/sounds/hosson khamsa/hosson-12.m4a',
                    'العناية بالمتشابهات اللفظية 2' => 'uploads/sounds/hosson khamsa/hosson-13.m4a',
                    'العوائق' => 'uploads/sounds/hosson khamsa/hosson-14.m4a',
                ]),
                'image' => null,
                'type' => 'listen',
            ],
            [
                'name' => 'دفع مصاريف التقديم',
                'description' => 'دفع مصاريف التقديم 200 ج.م.',
                'urls' => json_encode(['كاش' => '01000000000', 'انستا باي' => 'eeeeee']),
                'image' => null,
                'type' => 'payment',
            ],
        ];

        foreach ($achievements as $achievement) {
            Achievement::create($achievement);
        }
    }
}

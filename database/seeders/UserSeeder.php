<?php

namespace Database\Seeders;

use App\Models\Admin;
use App\Models\Teacher;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class UserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $users = [
            [
                'name' => 'يحيى سمير محمد',
                'email' => 'ysamer2525@gmail.com',
                'phone' => '01097816172',
                'email_verified_at' => now(),
                'phone_verified_at' => now(),
                'password' => Hash::make('12345678'),
                'gender' => 'male',
                'birth_date' => '1990-05-15',
                'image' => fake()->imageUrl(),
                'is_active' => true,
            ],
            [
                'name' => 'محمد',
                'email' => 'mohamed@example.com',
                'phone' => '966512345678',
                'email_verified_at' => now(),
                'phone_verified_at' => now(),
                'password' => Hash::make('12345678'),
                'gender' => 'male',
                'birth_date' => '1990-05-15',
                'image' => fake()->imageUrl(),
                'is_active' => true,
            ],
            [
                'name' => 'فاطمة الزهراء',
                'email' => 'fatima.zahra@example.com',
                'phone' => '966512345679',
                'email_verified_at' => now(),
                'phone_verified_at' => now(),
                'password' => Hash::make('12345678'),
                'gender' => 'female',
                'birth_date' => '1995-08-10',
                'image' => fake()->imageUrl(),
                'is_active' => true,
            ],
            [
                'name' => 'أحمد مصطفى',
                'email' => 'ahmed.mostafa@example.com',
                'phone' => '966512345680',
                'email_verified_at' => now(),
                'phone_verified_at' => now(),
                'password' => Hash::make('12345678'),
                'gender' => 'male',
                'birth_date' => '1988-11-20',
                'image' => fake()->imageUrl(),
                'is_active' => true,
            ],
        ];

        foreach ($users as $user) {
            User::create($user);
        }

        Admin::create([
            'name' => 'المشرف',
            'email' => 'admin@test.com',
            'phone' => '1234567890',
            'email_verified_at' => now(),
            'phone_verified_at' => now(),
            'password' => Hash::make('12345678'),
            'gender' => 'male',
            'birth_date' => '1988-11-20',
            'image' => fake()->imageUrl(),
            'is_active' => true,
        ]);
        Teacher::create([
            'name' => 'معلم تجريبي',
            'email' => 'teacher@test.com',
            'phone' => '1234567890',
            'email_verified_at' => now(),
            'phone_verified_at' => now(),
            'password' => Hash::make('12345678'),
            'gender' => 'male',
            'birth_date' => '1988-11-20',
            'image' => fake()->imageUrl(),
            'is_active' => true,
        ]);
    }
}

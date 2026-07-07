<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class UserSeeder extends Seeder
{
    public function run()
    {
        // 管理者ログイン用アカウント
        User::updateOrCreate(
            ['email' => 'admin@example.com'],
            [
                'name' => '管理者',
                'password' => Hash::make('password'),
                'role' => 'admin',
            ]
        );

        // 一般スタッフ用アカウント（認可の練習用。今は admin と同じことができてしまう）
        User::updateOrCreate(
            ['email' => 'staff@example.com'],
            [
                'name' => '担当スタッフ',
                'password' => Hash::make('password'),
                'role' => 'staff',
            ]
        );
    }
}

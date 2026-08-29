<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class UsersSeeder extends Seeder
{
    public function run(): void
    {
        // 供本地开发和演示使用的固定账号；重复执行 db:seed 时会重置为已知密码。
        User::updateOrCreate(
            ['email' => 'test@example.com'],
            [
                'name' => '测试用户',
                'email_verified_at' => now(),
                'password' => Hash::make('password'),
            ],
        );

        User::factory()->count(10)->create();
    }
}

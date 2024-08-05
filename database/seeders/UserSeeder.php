<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class UserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        User::create([
            'id' => 1,
            'name' => 'Test1',
            'email' => 'test1@gmail.com',
            'password' => '12345678',
        ]);
        User::create([
            'id' => 2,
            'name' => 'Test2',
            'email' => 'test2@gmail.com',
            'password' => '12345678',
        ]);
        User::create([
            'id' => 3,
            'name' => 'Test3',
            'email' => 'test3@gmail.com',
            'password' => '12345678',
        ]);
        User::create([
            'id' => 4,
            'name' => 'Test4',
            'email' => 'test4@gmail.com',
            'password' => '12345678',
        ]);
    }
}

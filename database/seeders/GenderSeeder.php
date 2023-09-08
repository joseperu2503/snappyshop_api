<?php

namespace Database\Seeders;

use App\Models\Gender;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class GenderSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $rows = [
            ['id' => 1, 'name' => 'Men'],
            ['id' => 2, 'name' => 'Woman'],
            ['id' => 3, 'name' => 'Boy'],
            ['id' => 4, 'name' => 'Girl'],
        ];

        foreach ($rows as $row) {
            Gender::updateOrCreate(
                ['id' => $row['id']],
                [
                    'name' => $row['name'],
                ]
            );
        }
    }
}

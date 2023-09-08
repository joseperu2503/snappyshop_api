<?php

namespace Database\Seeders;

use App\Models\Size;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class SizeSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $rows = [
            ['id' => 1, 'name' => 'S'],
            ['id' => 2, 'name' => 'M'],
            ['id' => 3, 'name' => 'L'],
            ['id' => 4, 'name' => 'XL'],
        ];

        foreach ($rows as $row) {
            Size::updateOrCreate(
                ['id' => $row['id']],
                [
                    'name' => $row['name'],
                ]
            );
        }
    }
}

<?php

namespace Database\Seeders;

use App\Models\Brand;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class BrandSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $rows = [
            ['name' => 'Adidas'],
            // ['name' => 'Nike'],
            // ['name' => 'Puma'],
            // ['name' => 'Apple'],
            // ['name' => 'Samsung'],
        ];

        foreach ($rows as $row) {
            Brand::firstOrCreate(
                ['name' => $row['name']]
            );
        }
    }
}

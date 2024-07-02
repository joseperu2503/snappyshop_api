<?php

namespace Database\Seeders;

use App\Models\Category;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class CategorySeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $rows = [
            ['name' => 'Clothing and Fashion'],
            // ['name' => 'Electronics'],
            // ['name' => 'Home and Garden'],
            // ['name' => 'Health and Beauty'],
            // ['name' => 'Toys and Entertainment'],
            // ['name' => 'Pets'],
            // ['name' => 'Jewelry and Watches'],
        ];

        foreach ($rows as $row) {
            Category::firstOrCreate(
                ['name' => $row['name']]
            );
        }
    }
}

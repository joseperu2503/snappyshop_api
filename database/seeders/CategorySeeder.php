<?php

namespace Database\Seeders;

use App\Http\Controllers\SeedController;
use App\Models\Category;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\File;

class CategorySeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // $rows = [
        //     ['name' => 'Clothing and Fashion'],
        //     ['name' => 'Electronics'],
        //     ['name' => 'Outdoor Living'],
        //     //https://www.dickssportinggoods.com/f/outdoor-living-sale
        //     // ['name' => 'Health and Beauty'],
        //     // ['name' => 'Toys and Entertainment'],
        //     // ['name' => 'Pets'],
        //     // ['name' => 'Jewelry and Watches'],
        // ];

        // foreach ($rows as $row) {
        //     Category::firstOrCreate(
        //         ['name' => $row['name']]
        //     );
        // }

        $categories = SeedController::getJsonFile()['categories'];

        foreach ($categories as $category) {
            Category::firstOrCreate(
                ['name' => $category['name']]
            );
        }
    }
}

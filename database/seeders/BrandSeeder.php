<?php

namespace Database\Seeders;

use App\Http\Controllers\SeedController;
use App\Models\Brand;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\File;

class BrandSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // $rows = [
        //     ['name' => 'Adidas'],
        //     ['name' => 'Apple'],
        //     ['name' => 'Blackstone'],
        //     ['name' => 'Quest'],
        //     ['name' => 'GCI Outdoor'],
        //     // ['name' => 'Puma'],
        //     // ['name' => 'Samsung'],
        // ];

        // foreach ($rows as $row) {
        //     Brand::firstOrCreate(
        //         ['name' => $row['name']]
        //     );
        // }

        $brands = SeedController::getJsonFile()['brands'];

        foreach ($brands as $brand) {
            Brand::firstOrCreate(
                ['name' => $brand['name']]
            );
        }
    }
}

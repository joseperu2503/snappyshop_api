<?php

namespace Database\Seeders;

use App\Http\Controllers\SeedController;
use App\Models\Store;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class StoreSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $categories = SeedController::getJsonFile()['stores'];

        foreach ($categories as $category) {
            Store::firstOrCreate(
                [
                    'name' => $category['name'],
                    'description' => $category['description'],
                    'website' => $category['website'],
                    'phone' => $category['phone'],
                    'email' => $category['email'],
                    'facebook' => $category['facebook'],
                    'instagram' => $category['instagram'],
                    'logotype' => $category['logotype'],
                    'isotype' => $category['isotype'],
                    'backdrop' => $category['backdrop'],
                    'user_id' => 1,
                ]
            );
        }
    }
}

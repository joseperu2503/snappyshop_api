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
        $stores = SeedController::getJsonFile()['stores'];

        foreach ($stores as $store) {
            Store::firstOrCreate(
                [
                    'name' => $store['name'],
                    'description' => $store['description'],
                    'website' => $store['website'],
                    'phone' => $store['phone'],
                    'email' => $store['email'],
                    'facebook' => $store['facebook'],
                    'instagram' => $store['instagram'],
                    'logotype' => $store['logotype'],
                    'youtube' => $store['youtube'],
                    'isotype' => $store['isotype'],
                    'backdrop' => $store['backdrop'],
                    'user_id' => 1,
                    'is_active' => $store['is_active'],
                ]
            );
        }
    }
}

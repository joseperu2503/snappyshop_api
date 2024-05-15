<?php

namespace Database\Seeders;

use App\Models\PaymentMethod;
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
            ['name' => 'Visa'],
            ['name' => 'MasterCard'],
            ['name' => 'American Express'],
        ];

        foreach ($rows as $row) {
            PaymentMethod::firstOrCreate(
                ['name' => $row['name']]
            );
        }
    }
}

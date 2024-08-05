<?php

namespace Database\Seeders;

use App\Http\Controllers\ProductController;
use App\Http\Controllers\SeedController;
use App\Models\Product;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\File;
use Illuminate\Http\Request;

class ProductSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $products = SeedController::getJsonFile()['products'];

        foreach ($products as $product) {
            ProductController::storeSeed(new Request(
                [
                    'name' => $product['name'],
                    'description' => $product['description'],
                    'price' => $product['price'],
                    'stock' => $product['stock'],
                    'images' => $product['images'],
                    'store' => $product['store'],
                    'category' => $product['category'],
                    'colors' => $product['colors'],
                    'is_active' => $product['is_active'],
                    'discount' => $product['discount'],
                ]
            ));
        }
    }
}

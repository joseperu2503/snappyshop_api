<?php

namespace Database\Seeders;

use App\Models\Product;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\File;

class ProductSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $json = File::get(database_path('data/products.json'));

        $products = json_decode($json, true);
        foreach ($products as $product) {
            Product::create([
                'name' => $product['name'],
                'description' => $product['description'],
                'price' => $product['price'],
                'stock' => $product['stock'],
                'images' => $product['images'],
                'user_id' => $product['user_id'],
                'brand_id' => $product['brand_id'],
                'category_id' => $product['category_id'],
                'colors' => $product['colors'],
                'is_active' => $product['is_active'],
                'discount' => $product['discount'],
            ]);
        }
    }
}

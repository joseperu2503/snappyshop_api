<?php

namespace App\Http\Controllers;

use App\Http\Requests\ProductRequest;
use App\Http\Resources\ProductSeedResource;
use App\Models\Brand;
use App\Models\Category;
use App\Models\Product;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\File;

class SeedController extends Controller
{
    static public function getJsonFile()
    {
        $json = File::get(database_path('data/products.json'));
        return json_decode($json, true);
    }

    public function exportData()
    {
        $products = Product::all();
        $brands = Brand::select('name', 'image')->get();
        $categories = Category::select('name')->get();

        return [
            'products' => ProductSeedResource::collection($products),
            'brands' => $brands,
            'categories' => $categories,
        ];
    }

    public function importData(Request $request)
    {

        // Verifica que el archivo esté presente en la solicitud
        if (!$request->hasFile('file')) {
            return response()->json(['error' => 'No file uploaded'], 400);
        }
        $file = $request->file('file');

        $json = File::get($file);
        $json_decode = json_decode($json, true);

        $brands = $json_decode['brands'];

        foreach ($brands as $brand) {
            Brand::firstOrCreate(
                ['name' => $brand['name']]
            );
        }

        $categories = $json_decode['categories'];

        foreach ($categories as $category) {
            Category::firstOrCreate(
                ['name' => $category['name']]
            );
        }

        $products = $json_decode['products'];

        foreach ($products as $product) {
            ProductController::storeSeed(new Request(
                [
                    'name' => $product['name'],
                    'description' => $product['description'],
                    'price' => $product['price'],
                    'stock' => $product['stock'],
                    'images' => $product['images'],
                    'brand' => $product['brand'],
                    'category' => $product['category'],
                    'colors' => $product['colors'],
                    'is_active' => $product['is_active'],
                    'discount' => $product['discount'],
                ]
            ));
        }


        return [
            'success' => true
        ];
    }
}

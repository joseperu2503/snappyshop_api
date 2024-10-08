<?php

namespace App\Http\Controllers;

use App\Http\Requests\ProductRequest;
use App\Http\Resources\ProductSeedResource;
use App\Models\Category;
use App\Models\Product;
use App\Models\Store;
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
        $products = Product::inRandomOrder()->get();
        $stores = Store::select(
            'name',
            'description',
            'website',
            'phone',
            'email',
            'facebook',
            'instagram',
            'youtube',
            'logotype',
            'isotype',
            'backdrop',
            'is_active',
        )->get();

        $categories = Category::select('name')->get();

        return [
            'products' => ProductSeedResource::collection($products),
            'stores' => $stores,
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
                    'store' => $product['store'],
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

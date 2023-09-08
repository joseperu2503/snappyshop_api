<?php

namespace App\Http\Controllers;

use App\Http\Requests\ProductRequest;
use App\Http\Resources\ProductCollection;
use App\Http\Resources\ProductResource;
use App\Models\Brand;
use App\Models\Category;
use App\Models\Gender;
use App\Models\Product;
use App\Models\ProductGender;
use App\Models\ProductSize;
use App\Models\Size;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Throwable;

class ProductController extends Controller
{

    public function index()
    {
        $products = Product::all();
        return new ProductCollection($products);
    }

    public function store(ProductRequest $request)
    {
        DB::beginTransaction();
        try {
            $user_id = auth()->user()->id;
            $product = Product::create($request->all() + ['user_id' => $user_id]);
            $this->createProductSizes($product, $request);
            $this->createProductGenders($product, $request);


            DB::commit();
            return [
                'success' => true,
                'message' => 'Product registered successfully'
            ];
        } catch (Throwable $e) {
            DB::rollBack();

            return response()->json([
                'message' => $e->getMessage()
            ], 500);
        }
    }

    public function show(Product $product)
    {
        return new ProductResource($product);
    }

    public function update(ProductRequest $request, Product $product)
    {
        $product->update($request->all());
        $this->createProductSizes($product, $request);
        $this->createProductGenders($product, $request);
        return [
            'success' => true,
            'message' => 'Product updated successfully'
        ];
    }

    public function destroy(Product $product)
    {
        foreach ($product->product_genders as $product_gender) {
            $product_gender->delete();
        }
        foreach ($product->product_sizes as $product_size) {
            $product_size->delete();
        }
        $product->delete();
        return [
            'success' => true,
            'message' => 'Product deleted successfully'
        ];
    }

    public function createProductSizes($product, $request)
    {
        foreach ($product->product_sizes as $product_size) {
            if (!in_array($product_size->size_id, $request->sizes)) {
                $product_size->delete();
            }
        }

        foreach ($request->sizes as $size) {
            ProductSize::updateOrCreate(['product_id' => $product->id, 'size_id' => $size]);
        }
    }

    public function createProductGenders($product, $request)
    {
        foreach ($product->product_genders as $product_gender) {
            if (!in_array($product_gender->gender_id, $request->genders)) {
                $product_gender->delete();
            }
        }

        foreach ($request->genders as $gender) {
            ProductGender::updateOrCreate(['product_id' => $product->id, 'gender_id' => $gender]);
        }
    }

    public function formData()
    {
        $genders = Gender::all();
        $brands = Brand::all();
        $category = Category::all();
        $sizes = Size::all();

        return [
            'genders' => $genders,
            'brands' => $brands,
            'category' => $category,
            'sizes' => $sizes,
        ];
    }
}

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
    public function index(Request $request)
    {
        $user = auth()->user();

        $products = Product::orderBy('id', 'desc');
        if ($user) {
            $products = $products->leftJoin('favorites', function ($join) use ($user) {
                $join->on('products.id', '=', 'favorites.product_id')
                    ->where('favorites.user_id', $user->id);
            })
                ->select('products.*', DB::raw('IF(favorites.product_id IS NOT NULL, true, false) as is_favorite'));
        }

        if ($request->search && $request->search != '') {
            $searchResults = Product::search($request->search)->get();
            $productIds = $searchResults->pluck('id')->toArray();
            $products = $products->whereIn('products.id', $productIds);
        }

        if ($request->min_price) {
            $products = $products->where('price', '>=', $request->min_price);
        }
        if ($request->max_price) {
            $products = $products->where('price', '<=', $request->max_price);
        }
        if ($request->brand_id) {
            $products = $products->where('brand_id', $request->brand_id);
        }
        if ($request->category_id) {
            $products = $products->where('category_id', $request->category_id);
        }

        $products = $products->paginate(10);

        return new ProductCollection($products);
    }

    public function myProducts()
    {
        $user = auth()->user();

        $user_id = auth()->user()->id;
        $products = Product::leftjoin('favorites', function ($join) use ($user) {
            $join->on('products.id', '=', 'favorites.product_id')
                ->where('favorites.user_id', $user->id);
        })
            ->select('products.*', DB::raw('IF(favorites.product_id IS NOT NULL, true, false) as is_favorite'))
            ->where('products.user_id', $user_id)->orderBy('id', 'desc')->paginate(10);
        return new ProductCollection($products);
    }

    public function store(ProductRequest $request)
    {
        DB::beginTransaction();
        try {
            $user_id = auth()->user()->id;
            $product = Product::create($request->all() + ['user_id' => $user_id]);

            if ($request->sizes) {
                $this->createProductSizes($product, $request->sizes);
            }

            if ($request->genders) {
                $this->createProductGenders($product, $request->genders);
            }

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
        $user = auth()->user();

        $product = Product::where('products.id', $product->id);
        if ($user) {
            $product = $product->leftJoin('favorites', function ($join) use ($user) {
                $join->on('products.id', '=', 'favorites.product_id')
                    ->where('favorites.user_id', $user->id);
            })
                ->select('products.*', DB::raw('IF(favorites.product_id IS NOT NULL, true, false) as is_favorite'));
        }

        $product = $product->first();

        return new ProductResource($product);
    }

    public function update(ProductRequest $request, Product $product)
    {
        $user_id = auth()->user()->id;

        if ($product->user_id != $user_id) {
            return response()->json([
                'success' => false,
                'message' => "You don't have permission to update this product"
            ], 200);
        }
        DB::beginTransaction();
        try {

            $product->update($request->all());
            if ($request->sizes) {
                $this->createProductSizes($product, $request->sizes);
            }

            if ($request->genders) {
                $this->createProductGenders($product, $request->genders);
            }

            DB::commit();
            return response()->json([
                'success' => true,
                'message' => 'Product updated successfully'
            ], 200);
        } catch (Throwable $e) {
            DB::rollBack();

            return response()->json([
                'message' => $e->getMessage()
            ], 500);
        }
    }

    public function destroy(Product $product)
    {
        $user_id = auth()->user()->id;

        if ($product->user_id != $user_id) {
            return response()->json([
                'success' => false,
                'message' => "You don't have permission to delete this product"
            ], 200);
        }

        DB::beginTransaction();
        try {
            foreach ($product->product_genders as $product_gender) {
                $product_gender->delete();
            }
            foreach ($product->product_sizes as $product_size) {
                $product_size->delete();
            }
            $product->delete();

            DB::commit();

            return [
                'success' => true,
                'message' => 'Product deleted successfully'
            ];
        } catch (Throwable $e) {
            DB::rollBack();

            return response()->json([
                'message' => $e->getMessage()
            ], 500);
        }
    }

    public function createProductSizes($product, $sizes)
    {
        foreach ($product->product_sizes as $product_size) {
            if (!in_array($product_size->size_id, $sizes)) {
                $product_size->delete();
            }
        }

        foreach ($sizes as $size) {
            ProductSize::updateOrCreate(['product_id' => $product->id, 'size_id' => $size]);
        }
    }

    public function createProductGenders($product, $genders)
    {
        foreach ($product->product_genders as $product_gender) {
            if (!in_array($product_gender->gender_id, $genders)) {
                $product_gender->delete();
            }
        }

        foreach ($genders as $gender) {
            ProductGender::updateOrCreate(['product_id' => $product->id, 'gender_id' => $gender]);
        }
    }

    public function formData()
    {
        $genders = Gender::all();
        $brands = Brand::all();
        $categories = Category::all();
        $sizes = Size::all();

        return [
            'genders' => $genders,
            'brands' => $brands,
            'categories' => $categories,
            'sizes' => $sizes,
        ];
    }

    public function filterData()
    {
        $genders = Gender::select('id', 'name')->get();
        $brands = Brand::select('id', 'name')->get();
        $categories = Category::select('id', 'name')->get();
        $sizes = Size::select('id', 'name')->get();

        return [
            'genders' => array_merge([['id' => null, 'name' => 'All genders']], $genders->toArray()),
            'brands' => array_merge([['id' => null, 'name' => 'All brands']], $brands->toArray()),
            'categories' => array_merge([['id' => null, 'name' => 'All categories']], $categories->toArray()),
            'sizes' => array_merge([['id' => null, 'name' => 'All sizes']], $sizes->toArray()),
        ];
    }
}

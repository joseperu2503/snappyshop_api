<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Http\Requests\ProductRequest;
use App\Http\Resources\ProductCollection;
use App\Http\Resources\ProductResource;
use App\Http\Resources\StoreResource;
use App\Models\Category;
use App\Models\Gender;
use App\Models\Product;
use App\Models\ProductGender;
use App\Models\ProductSize;
use App\Models\Size;
use App\Models\Store;
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
        if ($request->store_id) {
            $products = $products->where('store_id', $request->store_id);
        }
        if ($request->category_id) {
            $products = $products->where('category_id', $request->category_id);
        }

        $products = $products->where('is_active', true)->paginate(10);

        return $this->paginateMapper(new ProductCollection($products));
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
            ->leftjoin('stores', 'stores.id', '=', 'products.store_id')
            ->where('stores.user_id', $user_id)
            ->where('products.is_active', true)
            ->orderBy('id', 'desc')->paginate(10);
        return $this->paginateMapper(new ProductCollection($products));
    }

    public function store(ProductRequest $request)
    {
        DB::beginTransaction();
        try {
            $user = auth()->user();
            $store = $user->store;
            if ($store) {
                return response()->json([
                    'success' => false,
                    'message' => "You don't have permission to register products"
                ], 401);
            }

            $product = Product::create($request->all() + ['store_id' => $store->store_id]);

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

        $store_related_products = Product::orderBy('id', 'desc');
        if ($user) {
            $store_related_products = $store_related_products->leftJoin('favorites', function ($join) use ($user) {
                $join->on('products.id', '=', 'favorites.product_id')
                    ->where('favorites.user_id', $user->id);
            })
                ->select('products.*', DB::raw('IF(favorites.product_id IS NOT NULL, true, false) as is_favorite'));
        }
        $store_related_products = $store_related_products->orderByRaw('FIELD(category_id, ' . implode(',', [$product->category_id]) . ')')
            ->where('products.id', '!=', $product->id)
            ->where('store_id', $product->store_id)
            ->where('is_active', true)
            ->take(10)->get();

        return [
            'product' => new ProductResource($product),
            'store_related_products' => new ProductCollection($store_related_products),
            'store' => new StoreResource($product->store),
        ];
    }

    public function update(ProductRequest $request, Product $product)
    {
        $user_id = auth()->user()->id;

        if ($product->store->user->user_id != $user_id) {
            return response()->json([
                'success' => false,
                'message' => "You don't have permission to update this product"
            ], 401);
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

        if ($product->store->user->user_id != $user_id) {
            return response()->json([
                'success' => false,
                'message' => "You don't have permission to delete this product"
            ], 401);
        }

        DB::beginTransaction();
        try {

            $product->update([
                'is_active' => false,
            ]);

            DB::commit();

            return response()->json([
                'success' => true,
                'message' => 'Product deleted successfully'
            ], 200);
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
        $categories = Category::all();
        $stores = Store::all();
        $sizes = Size::all();

        return [
            'genders' => $genders,
            'stores' => $stores,
            'categories' => $categories,
            'sizes' => $sizes,
        ];
    }

    public function filterData()
    {
        $genders = Gender::select('id', 'name')->get();
        $stores = Store::select('id', 'name')->get();
        $categories = Category::select('id', 'name')->get();
        $sizes = Size::select('id', 'name')->get();

        return [
            'genders' => array_merge([['id' => null, 'name' => 'All genders']], $genders->toArray()),
            'stores' => array_merge([['id' => null, 'name' => 'All stores']], $stores->toArray()),
            'categories' => array_merge([['id' => null, 'name' => 'All categories']], $categories->toArray()),
            'sizes' => array_merge([['id' => null, 'name' => 'All sizes']], $sizes->toArray()),
        ];
    }

    static public function storeSeed(Request $request)
    {
        DB::beginTransaction();
        try {
            if (!$request->store) {
                return;
            }

            $store = Store::where('name', $request->store)->first();
            if (!$store) {
                return;
            }

            if (!$request->category) {
                return;
            }

            $category = Category::where('name', $request->category)->first();
            if (!$category) {
                $category = Category::create([
                    'name' => $request->category
                ]);
            }

            $product = Product::create(
                [
                    'name' =>  $request->name,
                    'description' =>  $request->description,
                    'price' =>  $request->price,
                    'stock' =>  $request->stock,
                    'images' =>  $request->images,
                    'colors' =>  $request->colors,
                    'is_active' =>  $request->is_active,
                    'discount' =>  $request->discount,
                    'store_id' =>  $store->id,
                    'category_id' =>  $category->id,
                ]
            );

            if ($request->sizes) {
                self::createProductSizes($product, $request->sizes);
            }

            if ($request->genders) {
                self::createProductGenders($product, $request->genders);
            }

            DB::commit();
        } catch (Throwable $e) {
            DB::rollBack();

            throw $e;
        }
    }
}

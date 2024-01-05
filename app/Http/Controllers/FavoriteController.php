<?php

namespace App\Http\Controllers;

use App\Http\Requests\FavoriteRequest;
use App\Http\Resources\ProductCollection;
use App\Models\Favorite;
use App\Models\Product;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Throwable;

class FavoriteController extends Controller
{
    public function toggleFavorite(FavoriteRequest $request)
    {
        DB::beginTransaction();
        try {

            $user = auth()->user();
            foreach ($user->product_carts as $product_cart) {
                if (!in_array($product_cart->product_id, array_column($request->products, 'id'))) {
                    $product_cart->delete();
                }
            }

            $product_id = $request->product_id;
            $favorite = Favorite::where('user_id', $user->id)->where('product_id', $product_id)->first();

            if ($request->is_favorite == true) {
                if ($favorite) {
                    return [
                        'success' => true,
                        'message' => 'The product was already in favorites.',
                        'data' => true,
                    ];
                } else {
                    Favorite::create([
                        'product_id' => $product_id,
                        'user_id' => $user->id,
                    ]);
                    DB::commit();
                    return [
                        'success' => true,
                        'message' => 'The product was added to favorites.',
                        'data' => true,
                    ];
                }
            }


            if ($request->is_favorite == false) {
                if ($favorite) {
                    $favorite->delete();
                    DB::commit();
                    return [
                        'success' => true,
                        'message' => 'The product was removed from favorites.',
                        'data' => true,
                    ];
                } else {
                    return [
                        'success' => true,
                        'message' => 'The product is not in favorites.',
                        'data' => true,
                    ];
                }
            }
        } catch (Throwable $e) {
            DB::rollBack();

            return response()->json([
                'message' => $e->getMessage()
            ], 500);
        }
    }

    public function myFavoriteProducts()
    {
        $user = auth()->user();

        $products = Product::rightJoin('favorites', function ($join) use ($user) {
            $join->on('favorites.product_id', '=', 'products.id')
                ->where('favorites.user_id', $user->id);
        })->orderBy('products.id', 'desc')->paginate(10);

        return new ProductCollection($products);
    }
}

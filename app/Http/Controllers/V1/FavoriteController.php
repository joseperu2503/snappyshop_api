<?php

namespace App\Http\Controllers\V2;

use App\Http\Controllers\Controller;
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
                        'data' => false,
                    ];
                } else {
                    return [
                        'success' => true,
                        'message' => 'The product is not in favorites.',
                        'data' => false,
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

        $products = Product::select('products.*')->join('favorites', function ($join) use ($user) {
            $join->on('favorites.product_id', '=', 'products.id')
                ->where('favorites.user_id', $user->id);
        })
            ->select('products.*', DB::raw('IF(favorites.product_id IS NOT NULL, true, false) as is_favorite'))
            ->orderBy('products.id', 'desc')->paginate(10);

        return new ProductCollection($products);
    }
}

<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Http\Requests\CartRequest;
use App\Http\Resources\V1\CartCollection;
use App\Models\ProductCart;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Throwable;

class CartController extends Controller
{
    public function store(CartRequest $request)
    {
        DB::beginTransaction();
        try {

            $user = auth()->user();
            foreach ($user->product_carts as $product_cart) {
                if (!in_array($product_cart->product_id, array_column($request->products, 'id'))) {
                    $product_cart->delete();
                }
            }

            foreach ($request->products as $product) {
                ProductCart::updateOrCreate(
                    [
                        'product_id' => $product['id'],
                        'user_id' => $user->id,
                    ],
                    [
                        'quantity' => $product['quantity'],
                    ]
                );
            }

            DB::commit();
            return [
                'success' => true,
                'message' => 'Cart registered successfully',
                'data' => $this->myCart(),
            ];
        } catch (Throwable $e) {
            DB::rollBack();

            return response()->json([
                'message' => $e->getMessage()
            ], 500);
        }
    }

    public function myCart()
    {
        $user_id = auth()->user()->id;
        $product_carts = ProductCart::where('user_id', $user_id)->orderBy('id', 'desc')->get();

        $totalAmount = 0;
        foreach ($product_carts as $productCart) {
            $price = $productCart->product->price;
            if ($productCart->product->discount) {
                $price = $price * (1 - $productCart->product->discount / 100);
            }

            $totalAmount += $price * $productCart->quantity;
        }

        return [
            'total_amount' => $totalAmount,
            'products' => new CartCollection($product_carts),
        ];
    }
}

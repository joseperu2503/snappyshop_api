<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Http\Requests\CartRequest;
use App\Http\Resources\CartCollection;
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

        $subtotal = 0;
        foreach ($product_carts as $productCart) {
            $subtotal += $productCart->product->sale_price * $productCart->quantity;
        }

        $shipping_fee = $subtotal == 0 ? 0 : 10;
        $total = $subtotal + $shipping_fee;

        return [
            'subtotal' => $subtotal,
            'shipping_fee' => $shipping_fee,
            'total_amount' => $total,
            'products' => new CartCollection($product_carts),
        ];
    }
}

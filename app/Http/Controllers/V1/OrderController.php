<?php

namespace App\Http\Controllers\V1;

use App\Http\Controllers\Controller;
use App\Http\Requests\OrderRequest;
use App\Http\Resources\OrderCollection;
use App\Models\Order;
use App\Models\Product;
use App\Models\ProductOrder;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Throwable;

class OrderController extends Controller
{
    public function store(OrderRequest $request)
    {
        DB::beginTransaction();
        try {

            $products = Product::whereIn('id', $request->products)->get();
            $total_amount = 0;
            foreach ($products as  $product) {
                $total_amount = $total_amount + $product->price;
            }

            $user_id = auth()->user()->id;
            $order = Order::create([
                'user_id' => $user_id,
                'total_amount' => $total_amount,
            ]);

            $this->createProductOrder($order, $products);


            DB::commit();
            return [
                'success' => true,
                'message' => 'Order registered successfully'
            ];
        } catch (Throwable $e) {
            DB::rollBack();

            return response()->json([
                'message' => $e->getMessage()
            ], 500);
        }
    }

    public function createProductOrder(Order $order, $products)
    {
        foreach ($products as $product) {
            ProductOrder::create([
                'order_id' => $order->id,
                'product_id' => $product->id,
                'price' => $product->price,
                'discount' => $product->discount,
            ]);
        }
    }

    public function myOrders()
    {
        $user_id = auth()->user()->id;
        $orders = Order::where('user_id', $user_id)->orderBy('id', 'desc')->paginate(10);

        return new OrderCollection($orders);
    }
}

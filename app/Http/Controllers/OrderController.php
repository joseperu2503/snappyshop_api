<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Http\Requests\OrderRequest;
use App\Http\Resources\V2\OrderCollection;
use App\Http\Resources\V2\OrderResource;
use App\Models\Address;
use App\Models\Order;
use App\Models\OrderStatus;
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

            $user_id = auth()->user()->id;
            $address = Address::find($request->address_id);
            $shipping_fee = 10;

            if ($address->user_id != $user_id) {
                return response()->json([
                    'success' => false,
                    'message' => "The address does not belong to you."
                ], 401);
            }

            $total_amount = 0;
            foreach ($request->products as $product_request) {
                $product = Product::find($product_request['id']);

                if (!$product->is_active) {
                    return response()->json([
                        'success' => false,
                        'message' => "One or more products are no longer available"
                    ], 404);
                }
                if (!$product->stock) {
                    return response()->json([
                        'success' => false,
                        'message' => "One or more products are out of stock"
                    ], 404);
                }

                $price = $product->price;
                if ($product->discount) {
                    $price =  $price * (1 - $product->discount / 100);
                }

                $total_amount = $total_amount + $price * $product_request['quantity'];
            }

            $order = Order::create([
                'user_id' => $user_id,
                'total_amount' => $total_amount,
                'shipping_fee' => $shipping_fee,
                'card_number' => $request->card_number,
                'card_holder_name' => $request->card_holder_name,
                'order_status_id' => 1,
                'address_id' => $request->address_id,
                'payment_method_id' => $request->payment_method_id,
            ]);

            foreach ($request->products as $product_request) {
                $product = Product::find($product_request['id']);
                ProductOrder::create([
                    'order_id' => $order->id,
                    'product_id' => $product->id,
                    'price' => $product->price,
                    'discount' => $product->discount,
                    'quantity' => $product_request['quantity'],
                ]);
            }

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

    public function myOrders(Request $request)
    {
        $user_id = auth()->user()->id;
        $orders = Order::where('user_id', $user_id);
        if ($request->order_status_id) {
            $orders = $orders->where('order_status_id', $request->order_status_id);
        }

        $orders = $orders->orderBy('id', 'desc')->paginate(10);

        return new OrderCollection($orders);
    }

    public function show(Order $order)
    {
        $user_id = auth()->user()->id;

        if ($order->user_id != $user_id) {
            return response()->json([
                'success' => false,
                'message' => "You don't have permission to see this order"
            ], 401);
        }

        return new OrderResource($order);
    }

    public function orderStatuses()
    {
        $order_statuses = OrderStatus::select('id', 'name')->get();

        return $order_statuses;
    }
}

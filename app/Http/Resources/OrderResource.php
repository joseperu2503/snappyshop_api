<?php

namespace App\Http\Resources;

use App\Http\Resources\ProductResource;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class OrderResource extends JsonResource
{
    public static $wrap = null;

    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'user' => [
                'id' => $this->user->id,
                'name' => $this->user->name,
            ],
            'products' => $this->product_orders->transform(function ($product_order) {
                return [
                    'price' => $product_order->price,
                    'discount' => $product_order->discount,
                    'quantity' => $product_order->quantity,
                    'product_detail' => new ProductResource($product_order->product)
                ];
            }),
            'total_amount' => $this->total_amount,
            'shipping_fee' => $this->shipping_fee,
            'card_number' => $this->card_number,
            'order_status' => [
                'id' => $this->order_status->id,
                'name' => $this->order_status->name,
            ],
            'address' => [
                'id' => $this->address->id,
                'name' => $this->address->name,
            ],
            'payment_method' => [
                'id' => $this->payment_method->id,
                'name' => $this->payment_method->name,
            ],
            'created_at' => $this->created_at,
        ];
    }
}

<?php

namespace App\Http\Resources\V2;

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
                    'product_detail' => new ProductResource($product_order->product)
                ];
            }),
            'total_amount' => $this->total_amount,
            'created_at' => $this->created_at,
        ];
    }
}

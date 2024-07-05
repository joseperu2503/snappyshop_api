<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\ResourceCollection;
use Illuminate\Http\Resources\Json\JsonResource;

class OrderCollection extends ResourceCollection
{
    public static $wrap = null;
    public $collects = OrderItem::class;

    /**
     * Transform the resource collection into an array.
     *
     * @return array<int|string, mixed>
     */
    public function toArray($request)
    {
        return [
            'results' => $this->collection,
            'info' => [
                'per_page' => $this->perPage(),
                'current_page' => $this->currentPage(),
                'last_page' => $this->lastPage()
            ],
        ];
    }

    public function toResponse($request)
    {
        return JsonResource::toResponse($request);
    }
}

class OrderItem extends JsonResource
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
            'total_amount' => $this->total_amount,
            'shipping_fee' => $this->shipping_fee,
            'card_number' => $this->card_number,
            'order_status' => [
                'id' => $this->order_status->id,
                'name' => $this->order_status->name,
            ],
            'address' => new AddressResource($this->address),
            'payment_method' => [
                'id' => $this->payment_method->id,
                'name' => $this->payment_method->name,
            ],
            'items' =>  $this->product_orders->sum('quantity'),
            'created_at' => $this->created_at,
        ];
    }
}

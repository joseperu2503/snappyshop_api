<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class ProductResource extends JsonResource
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
            'name' => $this->name,
            'description' => $this->description,
            'price' => $this->price,
            'stock' => $this->stock,
            'images' => $this->images,
            'brand_id' => $this->brand_id,
            'category_id' => $this->category_id,
            'colors' => $this->colors,
            'sizes' => $this->sizes->map(function ($row) {
                return $row->id;
            }),
            'genders' => $this->genders->map(function ($row) {
                return $row->id;
            }),
            'free_shipping' => $this->free_shipping,
            'created_at' => $this->created_at->format('d-m-Y'),
        ];
    }
}

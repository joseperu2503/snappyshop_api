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
            'store' => $this->store ? [
                'id' => $this->store->id,
                'name' => $this->store->name,
            ] : null,
            'category' => $this->category ? [
                'id' => $this->category->id,
                'name' => $this->category->name,
            ] : null,
            'colors' => $this->colors,
            'sizes' => $this->sizes->map(function ($size) {
                return [
                    'id' => $size->id,
                    'name' => $size->name,
                ];
            }),
            'genders' => $this->genders->map(function ($gender) {
                return [
                    'id' => $gender->id,
                    'name' => $gender->name,
                ];
            }),
            'store' => [
                'id' => $this->store->id,
                'name' => $this->store->name,
            ],
            'discount' => $this->discount,
            'created_at' => $this->created_at,
            'is_favorite' => boolval($this->is_favorite),
        ];
    }
}

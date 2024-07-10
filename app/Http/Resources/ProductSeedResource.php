<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class ProductSeedResource extends JsonResource
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
            'name' => $this->name,
            'description' => $this->description,
            'price' => $this->price,
            'stock' => $this->stock,
            'images' => $this->images,
            'brand' => $this->brand ?  $this->brand->name : null,
            'category' => $this->category ? $this->category->name : null,
            'colors' => $this->colors,
            'sizes' => $this->sizes->map(function ($size) {
                return $size->id;
            }),
            'genders' => $this->genders->map(function ($gender) {
                return  $gender->id;
            }),
            'discount' => $this->discount,
            'is_active' => $this->is_active,
        ];
    }
}

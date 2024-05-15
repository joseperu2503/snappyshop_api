<?php

namespace App\Http\Resources\V2;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class AddressResource extends JsonResource
{
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
            'address' => $this->address,
            'detail' => $this->detail,
            'phone' => $this->phone,
            'recipient_name' => $this->recipient_name,
            'references' => $this->references,
            'latitude' => $this->latitude,
            'longitude' => $this->longitude,
            'primary' => $this->primary,
            'created_at' => $this->created_at,
        ];
    }
}

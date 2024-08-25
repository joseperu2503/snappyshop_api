<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class AddressResource extends JsonResource
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
            'address' => $this->address,
            'detail' => $this->detail,
            'phone' => $this->phone,
            'recipient_name' => $this->recipient_name,
            'references' => $this->references,
            'latitude' => $this->latitude,
            'longitude' => $this->longitude,
            'default' => $this->default,
            'country' => $this->country,
            'locality' => $this->locality,
            'plus_code' => $this->plus_code,
            'postal_code' => $this->postal_code,
        ];
    }
}

<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class AddressRequest extends FormRequest
{
    public function rules()
    {
        return [
            'address' => 'required|string',
            'detail' => 'required|string',
            'recipient_name' => 'required|string',
            'phone' => 'required|numeric',
            'references' => 'present',
            'latitude' => 'numeric',
            'longitude' => 'numeric',
        ];
    }
}

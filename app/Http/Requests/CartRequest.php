<?php

namespace App\Http\Requests;

use App\Rules\HexColor;
use Illuminate\Foundation\Http\FormRequest;

class CartRequest extends FormRequest
{
    public function authorize()
    {
        return true;
    }

    public function rules()
    {
        return [
            'products' => 'required|array|min:1',
            'products.*.id' => 'required|exists:products,id',
            'products.*.quantity' => 'required|integer|min:0',
        ];
    }

    public function messages()
    {
        return [
            'products.required' => 'The :attribute field is required.',
            'products.*.id.exists' => 'The :attribute field must be a valid product id.',
        ];
    }
}

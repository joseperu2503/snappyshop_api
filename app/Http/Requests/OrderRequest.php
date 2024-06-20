<?php

namespace App\Http\Requests;

use App\Rules\HexColor;
use Illuminate\Foundation\Http\FormRequest;

class OrderRequest extends FormRequest
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
            'products.*.quantity' => 'required|integer|min:1',
            'card_number' => 'required|string',
            'card_holder_name' => 'required|string',
            'address_id' =>  'required|exists:addresses,id',
            'payment_method_id' =>  'required|exists:payment_methods,id',

        ];
    }

    public function messages()
    {
        return [
            'products.required' => 'The :attribute field is required.',
            'products.*.id.exists' => 'The :attribute field must be a valid product id.',
            'brand_id.exists' => 'The brand_id field must be a valid address id.',
            'payment_method_id.exists' => 'The payment_method_id field must be a valid payment method id.',
        ];
    }
}

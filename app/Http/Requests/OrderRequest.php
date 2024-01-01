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
            'products.*' => 'exists:products,id',
        ];
    }

    public function messages()
    {
        return [
            'products.required' => 'The :attribute field is required.',
            'products.*.exists' => 'The :attribute field must be a valid product id.',

        ];
    }
}

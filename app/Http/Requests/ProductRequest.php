<?php

namespace App\Http\Requests;

use App\Rules\HexColor;
use Illuminate\Foundation\Http\FormRequest;

class ProductRequest extends FormRequest
{
    public function authorize()
    {
        return true;
    }

    public function rules()
    {
        return [
            'name' => 'required',
            'description' => 'required',
            'brand_id' => 'numeric|nullable|exists:brands,id',
            'category_id' => 'numeric|nullable|exists:categories,id',
            'price' => 'required|numeric',
            'stock' => 'required|numeric|integer',
            'images' => 'array',
            'images.*' => 'url',
            'colors' => 'array',
            'colors.*' => [new HexColor],
            'sizes' => 'array',
            'sizes.*' => 'exists:sizes,id',
            'genders' => 'array',
            'genders.*' => 'exists:genders,id',
            'free_shipping' => 'boolean',
        ];
    }

    public function messages()
    {
        return [
            'brand_id.exists' => 'The brand_id field must be a valid brand id.',
            'category_id.exists' => 'The category_id field must be a valid category id.',
            'sizes.*.exists' => 'The :attribute field must be a valid size id.',
            'genders.*.exists' => 'The :attribute field must be a valid gender id.',
            'free_shipping.boolean' => 'The free_shipping field must be true or false.',
        ];
    }
}

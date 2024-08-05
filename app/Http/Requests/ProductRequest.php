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
            'name' => 'required|string',
            'description' => 'required|string',
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
            'discount' => 'numeric|integer|max:100|min:0|nullable',
        ];
    }

    public function messages()
    {
        return [
            'category_id.exists' => 'The category_id field must be a valid category id.',
            'sizes.*.exists' => 'The :attribute field must be a valid size id.',
            'genders.*.exists' => 'The :attribute field must be a valid gender id.',
        ];
    }
}

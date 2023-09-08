<?php

namespace App\Http\Requests;

use App\Rules\ArrayOfHexColorsRule;
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
            'brand_id' => 'numeric|nullable',
            'category_id' => 'numeric|nullable',
            'price' => 'required|numeric',
            'stock' => 'required|numeric',
            'images' => 'array',
            'images.*' => 'url',
            'colors' => [new ArrayOfHexColorsRule()]
        ];
    }

    public function messages()
    {
        return [
            'name.required' => 'Name is required.',
            'description.required' => 'Description is required.',
            'price.required' => 'Price is required.',
            'stock.required' => 'Stock is required.',
            'price.numeric' => 'Price must be a number.',
            'stock.numeric' => 'Stock must be a number.',
            'images.array' => 'Images must be an array.',
            'images.*.url' => 'Each image must be a valid URL.',

        ];
    }
}

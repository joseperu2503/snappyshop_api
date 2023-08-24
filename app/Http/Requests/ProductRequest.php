<?php

namespace App\Http\Requests;

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
            'price' => 'required|numeric',
            'stock' => 'required|numeric',
            'image' => 'required|url',
        ];
    }

    public function messages()
    {
        return [
            'name.required' => 'Name is required.',
            'price.required' => 'Price is required.',
            'stock.required' => 'Stock is required.',
            'price.numeric' => 'Price must be a number.',
            'stock.numeric' => 'Stock must be a number.',
            'image.required' => 'Image is required.',
            'image.url' => 'Image must be a url.',
        ];
    }
}

<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class FavoriteRequest extends FormRequest
{
    public function authorize()
    {
        return true;
    }

    public function rules()
    {
        return [
            'product_id' => 'required|exists:products,id',
            'is_favorite' => 'required|boolean',
        ];
    }

    public function messages()
    {
        return [
            'product_id.required' => 'The :attribute field is required.',
            'product_id.exists' => 'The :attribute field must be a valid product id.',
        ];
    }
}

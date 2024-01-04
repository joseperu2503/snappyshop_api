<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class ChangePasswordRequest extends FormRequest
{
    public function authorize()
    {
        return true;
    }

    public function rules()
    {
        return [
            'password' => 'required|string|confirmed',
        ];
    }

    public function messages()
    {
        return [
            'password.required' => 'Password is required.',
            'password.confirmed' => 'Please confirm your password.',
        ];
    }
}

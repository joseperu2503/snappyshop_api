<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class ChangePasswordExternalRequest extends FormRequest
{
    public function authorize()
    {
        return true;
    }

    public function rules()
    {
        return [
            'password' => 'required|string|confirmed',
            'email' => 'required|email|exists:users,email',
            'code' => 'required|string|digits:4',
            'uuid' => 'required|uuid|exists:verify_codes,uuid',
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

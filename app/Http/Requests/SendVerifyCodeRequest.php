<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class SendVerifyCodeRequest extends FormRequest
{
    public function authorize()
    {
        return true;
    }

    public function rules()
    {
        return [
            'email' => 'required|email',
        ];
    }

    public function messages()
    {
        return [
            'email.required' => 'Email is required.',
            'email.email' => 'Invalid email format.',
        ];
    }
}

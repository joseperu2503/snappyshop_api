<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class ValidateVerifyCodeRequest extends FormRequest
{
    public function authorize()
    {
        return true;
    }

    public function rules()
    {
        return [
            'email' => 'required|email|exists:users,email',
            'code' => 'required|string|digits:4',
            'uuid' => 'required|uuid|exists:verify_codes,uuid',
        ];
    }
}

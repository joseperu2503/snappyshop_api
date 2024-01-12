<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class ChangePersonalDataRequest extends FormRequest
{
    public function authorize()
    {
        return true;
    }

    public function rules()
    {
        return [
            'id' => 'required|exists:users,id',
            'email' => 'required|email|unique:users,email,' . $this->id,
            'name' => 'required|string',
            'profile_photo' => 'present|url|nullable',
        ];
    }

    public function messages()
    {
        return [
            'name.required' => 'Name is required.',
            'email.required' => 'Email is required.',
            'email.email' => 'Invalid email format.',
            'email.unique' => 'Email is already registered.',
        ];
    }
}

<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class UserRequest extends FormRequest
{
    public function authorize()
    {
        return true;
    }

    public function rules()
    {
        return [
            'email' => 'required|unique:users|email',
            'password' => 'required|confirmed',
            'name' => 'required',
        ];
    }

    public function messages()
    {
        return [
            'name.required' => 'El nombre es obligatorio',
            'email.required' => 'El Correo electrónico es obligatorio',
            'email.unique' => 'El correo ingresado ya está registrado',
            'password.required' => 'La contraseña es obligatoria',
            'password.confirmed' => 'Confirme su contraseña',
        ];
    }
}

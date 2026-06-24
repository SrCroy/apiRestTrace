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
            'password_actual' => 'required|string',
            'password_nueva' => 'required|string|min:6|confirmed',
        ];
    }

    public function messages()
    {
        return [
            'password_actual.required' => 'Debe proporcionar la contraseña actual',
            'password_nueva.required' => 'Debe proporcionar la nueva contraseña',
            'password_nueva.min' => 'La nueva contraseña debe tener mínimo 6 caracteres',
            'password_nueva.confirmed' => 'Las contraseñas no coinciden',
        ];
    }
}
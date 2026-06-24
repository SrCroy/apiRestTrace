<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class UpdateProfileRequest extends FormRequest
{
    public function authorize()
    {
        return true;
    }

    public function rules()
    {
        return [
            'nombre' => 'required|string|max:100',
            'apellido' => 'required|string|max:100',
            'biografia' => 'nullable|string|max:100',
            'peso_kg' => 'nullable|numeric|between:20,300',
            'altura_cm' => 'nullable|numeric|between:100,250',
            'fecha_nacimiento' => 'nullable|date|before:today',
        ];
    }

    public function messages()
    {
        return [
            'nombre.required' => 'El nombre es obligatorio',
            'nombre.string' => 'El nombre debe ser texto',
            'nombre.max' => 'El nombre no puede exceder 100 caracteres',
            'apellido.required' => 'El apellido es obligatorio',
            'apellido.string' => 'El apellido debe ser texto',
            'apellido.max' => 'El apellido no puede exceder 100 caracteres',
            'biografia.max' => 'La biografía no puede exceder 100 caracteres',
            'peso_kg.numeric' => 'El peso debe ser un número',
            'peso_kg.between' => 'El peso debe estar entre 20 y 300 kg',
            'altura_cm.numeric' => 'La altura debe ser un número',
            'altura_cm.between' => 'La altura debe estar entre 100 y 250 cm',
            'fecha_nacimiento.date' => 'La fecha debe ser válida',
            'fecha_nacimiento.before' => 'La fecha de nacimiento debe ser anterior a hoy',
        ];
    }
}
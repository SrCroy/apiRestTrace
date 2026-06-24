<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class ActualizarRutaRequest extends FormRequest
{
    public function authorize()
    {
        return true;
    }

    public function rules()
    {
        return [
            'nombre'              => 'sometimes|required|string|max:500',
            'descripcion'         => 'nullable|string|max:100',
            'distancia_km'        => 'sometimes|required|numeric|min:0.01',
            'desnivel_positivo_m' => 'nullable|numeric|min:0',
            'dificultad'          => 'sometimes|required|integer|between:1,6',
            'privacidad'          => 'sometimes|required|in:publico,amigos,privado',
            'tipo_deporte'        => 'sometimes|required|in:carrera,caminata,ciclismo,natacion,senderismo,montanismo,otro',
        ];
    }

    public function messages()
    {
        return [
            'nombre.max'          => 'El nombre no puede exceder 500 caracteres',
            'distancia_km.min'    => 'La distancia debe ser mayor a 0',
            'dificultad.between'  => 'La dificultad debe estar entre 1 y 6',
            'privacidad.in'       => 'Privacidad no válida. Opciones: publico, amigos, privado',
            'tipo_deporte.in'     => 'Tipo de deporte no válido',
        ];
    }
}

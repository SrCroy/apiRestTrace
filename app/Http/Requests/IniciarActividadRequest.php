<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class IniciarActividadRequest extends FormRequest
{
    public function authorize()
    {
        return true;
    }

    public function rules()
    {
        return [
            'titulo'       => 'required|string|max:500',
            'tipo_deporte' => 'required|in:carrera,caminata,ciclismo,natacion,senderismo,montanismo,otro',
            'dificultad'   => 'required|integer|between:1,6',
            'privacidad'   => 'required|in:publico,amigos,privado',
            'ruta_id'      => 'nullable|exists:rutas,id',
        ];
    }

    public function messages()
    {
        return [
            'titulo.required'       => 'El título es obligatorio',
            'titulo.max'            => 'El título no puede exceder 500 caracteres',
            'tipo_deporte.required' => 'El tipo de deporte es obligatorio',
            'tipo_deporte.in'       => 'Tipo de deporte no válido. Opciones: carrera, caminata, ciclismo, natacion, senderismo, montanismo, otro',
            'dificultad.required'   => 'La dificultad es obligatoria',
            'dificultad.between'    => 'La dificultad debe estar entre 1 y 6',
            'privacidad.required'   => 'La privacidad es obligatoria',
            'privacidad.in'         => 'Privacidad no válida. Opciones: publico, amigos, privado',
            'ruta_id.exists'        => 'La ruta especificada no existe',
        ];
    }
}

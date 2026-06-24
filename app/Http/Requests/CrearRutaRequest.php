<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class CrearRutaRequest extends FormRequest
{
    public function authorize()
    {
        return true;
    }

    public function rules()
    {
        return [
            'nombre'              => 'required|string|max:500',
            'descripcion'         => 'nullable|string|max:100',
            'distancia_km'        => 'required|numeric|min:0.01',
            'desnivel_positivo_m' => 'nullable|numeric|min:0',
            'dificultad'          => 'required|integer|between:1,6',
            'privacidad'          => 'required|in:publico,amigos,privado',
            'tipo_deporte'        => 'required|in:carrera,caminata,ciclismo,natacion,senderismo,montanismo,otro',
            'miniatura'           => 'nullable|image|mimes:jpeg,png,jpg|max:2048',
        ];
    }

    public function messages()
    {
        return [
            'nombre.required'        => 'El nombre de la ruta es obligatorio',
            'nombre.max'             => 'El nombre no puede exceder 500 caracteres',
            'distancia_km.required'  => 'La distancia es obligatoria',
            'distancia_km.min'       => 'La distancia debe ser mayor a 0',
            'dificultad.required'    => 'La dificultad es obligatoria',
            'dificultad.between'     => 'La dificultad debe estar entre 1 y 6',
            'privacidad.required'    => 'La privacidad es obligatoria',
            'privacidad.in'          => 'Privacidad no válida. Opciones: publico, amigos, privado',
            'tipo_deporte.required'  => 'El tipo de deporte es obligatorio',
            'tipo_deporte.in'        => 'Tipo de deporte no válido',
            'miniatura.image'        => 'La miniatura debe ser una imagen',
            'miniatura.mimes'        => 'La miniatura debe ser JPEG o PNG',
            'miniatura.max'          => 'La miniatura no puede exceder 2MB',
        ];
    }
}

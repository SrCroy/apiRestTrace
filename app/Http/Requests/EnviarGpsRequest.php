<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class EnviarGpsRequest extends FormRequest
{
    public function authorize()
    {
        return true;
    }

    public function rules()
    {
        return [
            'puntos'                  => 'required|array|min:1',
            'puntos.*.latitud'        => 'required|numeric|between:-90,90',
            'puntos.*.longitud'       => 'required|numeric|between:-180,180',
            'puntos.*.altitud_m'      => 'nullable|numeric',
            'puntos.*.velocidad_ms'   => 'nullable|numeric|min:0',
            'puntos.*.precision_m'    => 'nullable|numeric|min:0',
            'puntos.*.secuencia'      => 'required|integer|min:1',
            'puntos.*.registrado_en'  => 'required|date',
        ];
    }

    public function messages()
    {
        return [
            'puntos.required'                => 'Debe enviar al menos un punto GPS',
            'puntos.array'                   => 'Los puntos deben ser un arreglo',
            'puntos.min'                     => 'Debe enviar al menos un punto GPS',
            'puntos.*.latitud.required'      => 'La latitud es obligatoria para cada punto',
            'puntos.*.latitud.between'       => 'La latitud debe estar entre -90 y 90',
            'puntos.*.longitud.required'     => 'La longitud es obligatoria para cada punto',
            'puntos.*.longitud.between'      => 'La longitud debe estar entre -180 y 180',
            'puntos.*.secuencia.required'    => 'La secuencia es obligatoria para cada punto',
            'puntos.*.secuencia.min'         => 'La secuencia debe ser mayor a 0',
            'puntos.*.registrado_en.required' => 'El timestamp es obligatorio para cada punto',
            'puntos.*.registrado_en.date'    => 'El timestamp debe ser una fecha válida',
        ];
    }
}

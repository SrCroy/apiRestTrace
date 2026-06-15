<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class actividades extends Model
{
    use HasFactory;
    protected $table = 'actividades';

    protected $fillable = [
        'titulo',
        'tipo_deporte',
        'dificultad',
        'privacidad',
        'distancia_km',
        'duracion_seg',
        'duracion_pausa_segundos',
        'calorias',
        'desnivel_positivo_m',
        'desnivel_negativo_m',
        'ritmo_promedio',
        'ritmo_maximo',
        'velocidad_promedio_kmh',
        'velocidad_maxima_kmh',
        'inicio_lat',
        'inicio_lng',
        'final_lat',
        'final_lng',
        'nombre_lugar',
        'estado',
        'iniciada_en',
        'finalizada_en',
        'ruta_id',
        'id_usuario',
    ];

    protected function casts(): array
    {
        return [
            'dificultad'              => 'integer',
            'distancia_km'            => 'decimal:2',
            'duracion_seg'            => 'decimal:2',
            'duracion_pausa_segundos' => 'decimal:2',
            'calorias'                => 'decimal:2',
            'desnivel_positivo_m'     => 'decimal:2',
            'desnivel_negativo_m'     => 'decimal:2',
            'ritmo_promedio'          => 'decimal:2',
            'ritmo_maximo'            => 'decimal:2',
            'velocidad_promedio_kmh'  => 'decimal:2',
            'velocidad_maxima_kmh'    => 'decimal:2',
            'inicio_lat'              => 'decimal:7',
            'inicio_lng'              => 'decimal:7',
            'final_lat'               => 'decimal:7',
            'final_lng'               => 'decimal:7',
            'iniciada_en'             => 'datetime',
            'finalizada_en'           => 'datetime',
        ];
    }

    public function usuario()
    {
        return $this->belongsTo(User::class, 'id_usuario');
    }

    public function ruta()
    {
        return $this->belongsTo(rutas::class, 'ruta_id');
    }

    public function puntosGps()
    {
        return $this->hasMany(puntosGps::class, 'actividad_id');
    }

    public function publicaciones()
    {
        return $this->hasMany(publicaciones::class, 'actividad_id');
    }

    public function publicacionesGrupo()
    {
        return $this->hasMany(publicaciones_grupo::class, 'actividad_id');
    }

    public function logrosPersonalizados()
    {
        return $this->hasMany(logros_personalizados::class, 'actividad_id');
    }
}

<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class rutas extends Model
{
    use HasFactory;
    protected $table = 'rutas';
    protected $fillable = [
        'nombre',
        'descripcion',
        'distancia_km',
        'desnivel_positivo_m',
        'dificultad',
        'privacidad',
        'tipo_deporte',
        'veces_usada',
        'miniatura',
        'usuario_id',
    ];

    protected function casts(): array
    {
        return [
            'distancia_km'        => 'decimal:2',
            'desnivel_positivo_m' => 'decimal:2',
            'dificultad'          => 'integer',
            'veces_usada'         => 'integer',
        ];
    }

    public function usuario()
    {
        return $this->belongsTo(User::class, 'usuario_id');
    }

    public function puntosGps()
    {
        return $this->hasMany(RutaPuntoGps::class, 'ruta_id');
    }

    public function actividades()
    {
       return $this->hasMany(actividades::class, 'ruta_id');
    }

    public function publicaciones()
    {
        return $this->hasMany(publicaciones::class, 'ruta_id');
    }

    public function publicacionesGrupo()
    {
        return $this->hasMany(publicaciones_grupo::class, 'ruta_id');
    }
}

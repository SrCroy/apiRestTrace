<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class RutaPuntoGps extends Model
{
    protected $table = 'rutas_puntos_gps';

    public $timestamps = false;

    protected $fillable = [
        'ruta_id',
        'latitud',
        'longitud',
        'altitud_m',
        'secuencia',
    ];

    protected function casts(): array
    {
        return [
            'latitud'   => 'decimal:7',
            'longitud'  => 'decimal:7',
            'altitud_m' => 'decimal:2',
            'secuencia' => 'integer',
        ];
    }

    public function ruta()
    {
        return $this->belongsTo(rutas::class, 'ruta_id');
    }
}

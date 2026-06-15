<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class puntosGps extends Model
{
    use HasFactory;

    protected $table = 'puntos_gps';

    public $timestamps = false;

    protected $fillable = [
        'latitud',
        'longitud',
        'altitud_m',
        'velocidad_ms',
        'precision_m',
        'secuencia',
        'registrado_en',
        'actividad_id',
    ];

    protected function casts(): array
    {
        return [
            'latitud'       => 'decimal:7',
            'longitud'      => 'decimal:7',
            'altitud_m'     => 'decimal:2',
            'velocidad_ms'  => 'decimal:2',
            'precision_m'   => 'decimal:2',
            'secuencia'     => 'integer',
            'registrado_en' => 'datetime',
        ];
    }


    public function actividad()
    {
        return $this->belongsTo(actividades::class, 'actividad_id');
    }
}

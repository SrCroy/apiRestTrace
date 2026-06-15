<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class publicaciones extends Model
{
    use HasFactory;

    protected $table = 'publicaciones';

    protected $fillable = [
        'contenido',
        'privacidad',
        'estado',
        'usuario_id',
        'actividad_id',
        'ruta_id',
    ];

    protected function casts(): array
    {
        return [
            'created_at' => 'datetime',
            'updated_at' => 'datetime',
        ];
    }

    public function usuario()
    {
        return $this->belongsTo(User::class, 'usuario_id');
    }

    public function actividad()
    {
        return $this->belongsTo(actividades::class, 'actividad_id');
    }

    public function ruta()
    {
        return $this->belongsTo(rutas::class, 'ruta_id');
    }

    public function archivos()
    {
        return $this->hasMany(publicacion_archivo::class, 'publicacion_id');
    }

    public function reacciones()
    {
        return $this->morphMany(reacciones::class, 'reaccionable');
    }

    public function comentarios()
    {
        return $this->morphMany(comentarios::class, 'comentable');
    }
}

<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class publicaciones_grupo extends Model
{
    use HasFactory;

    protected $table = 'publicaciones_grupo';

    protected $fillable = [
        'contenido',
        'ruta_id',
        'actividad_id',
        'grupo_id',
        'usuario_id',
    ];

    protected function casts(): array
    {
        return [
            'created_at' => 'datetime',
            'updated_at' => 'datetime',
        ];
    }

    // ─── Relaciones ───────────────────────────────

    public function grupo()
    {
        return $this->belongsTo(grupos::class, 'grupo_id');
    }

    public function usuario()
    {
        return $this->belongsTo(User::class, 'usuario_id');
    }

    public function ruta()
    {
        return $this->belongsTo(rutas::class, 'ruta_id');
    }

    public function actividad()
    {
        return $this->belongsTo(actividades::class, 'actividad_id');
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

<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class logros_personalizados extends Model
{
    use HasFactory;

    protected $table = 'logros_personalizados';

    protected $fillable = [
        'nombre',
        'descripcion',
        'icono_url',
        'tipo_disparador',
        'valor_disparador',
        'actividad_id',
        'estado',
        'comentario_revision',
        'grupo_id',
        'propuesto_por',
        'revisado_por',
        'revisado_en',
    ];

    protected function casts(): array
    {
        return [
            'valor_disparador' => 'decimal:2',
            'revisado_en'      => 'datetime',
            'created_at'       => 'datetime',
            'updated_at'       => 'datetime',
        ];
    }

    // ─── Relaciones ───────────────────────────────

    public function grupo()
    {
        return $this->belongsTo(grupos::class, 'grupo_id');
    }

    public function propuestor()
    {
        return $this->belongsTo(User::class, 'propuesto_por');
    }

    public function revisor()
    {
        return $this->belongsTo(User::class, 'revisado_por');
    }

    public function actividad()
    {
        return $this->belongsTo(actividades::class, 'actividad_id');
    }

    public function usuariosLogros()
    {
        return $this->hasMany(usuarios_logros_personalizados::class, 'logro_personalizado_id');
    }
}

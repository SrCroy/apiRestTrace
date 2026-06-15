<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class reporte extends Model
{
    use HasFactory;

    protected $table = 'reportes';

    protected $fillable = [
        'reportable_tipo',
        'reportable_id',
        'motivo',
        'detalles',
        'estado',
        'revisado_en',
        'reportador_id',
        'revisado_por',
    ];

    protected function casts(): array
    {
        return [
            'revisado_en' => 'datetime',
            'created_at'  => 'datetime',
            'updated_at'  => 'datetime',
        ];
    }

    // ─── Relaciones ───────────────────────────────

    public function reportador()
    {
        return $this->belongsTo(User::class, 'reportador_id');
    }

    public function revisor()
    {
        return $this->belongsTo(User::class, 'revisado_por');
    }

    public function reportable()
    {
        return $this->morphTo(__FUNCTION__, 'reportable_tipo', 'reportable_id');
    }
}

<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class mensaje extends Model
{
    use HasFactory;

    protected $table = 'mensajes';

    protected $fillable = [
        'cuerpo',
        'leido_en',
        'conversacion_id',
        'remitente_id',
    ];

    protected function casts(): array
    {
        return [
            'leido_en'   => 'datetime',
            'created_at' => 'datetime',
            'updated_at' => 'datetime',
        ];
    }

    // ─── Relaciones ───────────────────────────────

    public function conversacion()
    {
        return $this->belongsTo(conversaciones::class, 'conversacion_id');
    }

    public function remitente()
    {
        return $this->belongsTo(User::class, 'remitente_id');
    }
}

<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class amistades extends Model
{
    use HasFactory;

    protected $table = 'amistades';

    protected $fillable = [
        'estado',
        'solicitante_id',
        'receptor_id',
    ];

    protected function casts(): array
    {
        return [
            'created_at' => 'datetime',
            'updated_at' => 'datetime',
        ];
    }

    // ─── Relaciones ───────────────────────────────

    public function solicitante()
    {
        return $this->belongsTo(User::class, 'solicitante_id');
    }

    public function receptor()
    {
        return $this->belongsTo(User::class, 'receptor_id');
    }
}

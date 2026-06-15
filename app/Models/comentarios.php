<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class comentarios extends Model
{
    use HasFactory;

    protected $table = 'comentarios';

    protected $fillable = [
        'comentable_tipo',
        'comentable_id',
        'cuerpo',
        'estado',
        'padre_id',
        'usuario_id',
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

    public function padre()
    {
        return $this->belongsTo(comentarios::class, 'padre_id');
    }

    public function respuestas()
    {
        return $this->hasMany(comentarios::class, 'padre_id');
    }

    public function comentable()
    {
        return $this->morphTo(__FUNCTION__, 'comentable_tipo', 'comentable_id');
    }
}

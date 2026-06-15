<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class conversaciones extends Model
{
    use HasFactory;

    protected $table = 'conversaciones';

    public $timestamps = false;

    protected $fillable = [
        'ultimo_mensaje_en',
        'usuario_uno_id',
        'usuario_dos_id',
        'creado_en',
    ];

    protected function casts(): array
    {
        return [
            'ultimo_mensaje_en' => 'datetime',
            'creado_en'         => 'datetime',
        ];
    }

    public function usuarioUno()
    {
        return $this->belongsTo(User::class, 'usuario_uno_id');
    }

    public function usuarioDos()
    {
        return $this->belongsTo(User::class, 'usuario_dos_id');
    }

    public function mensajes()
    {
        return $this->hasMany(mensaje::class, 'conversacion_id');
    }
}

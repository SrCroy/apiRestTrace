<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class logros extends Model
{
    use HasFactory;

    protected $table = 'logros';

    public $timestamps = false;

    protected $fillable = [
        'clave',
        'nombre',
        'descripcion',
        'icono',
        'tipo_disparador',
        'valor_disparador',
        'tipo_deporte',
        'creado_en',
    ];

    protected function casts(): array
    {
        return [
            'valor_disparador' => 'decimal:2',
            'creado_en'        => 'datetime',
        ];
    }

    public function usuarios()
    {
        return $this->belongsToMany(User::class, 'usuarios_logros', 'logro_id', 'usuario_id')
                    ->withPivot('obtenido_en');
    }

    public function usuariosLogros()
    {
        return $this->hasMany(usuarios_logros::class, 'logro_id');
    }
}

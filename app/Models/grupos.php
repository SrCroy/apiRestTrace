<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class grupos extends Model
{
    use HasFactory;

    protected $table = 'grupos';

    protected $fillable = [
        'nombre',
        'descripcion',
        'avatar',
        'privacidad',
        'total_miembros',
        'propietario_id',
    ];

    protected function casts(): array
    {
        return [
            'total_miembros' => 'integer',
        ];
    }

    public function propietario()
    {
        return $this->belongsTo(User::class, 'propietario_id');
    }

    public function usuarios()
    {
        return $this->belongsToMany(User::class, 'miembros_grupo', 'grupo_id', 'usuario_id')
                    ->withPivot('rol', 'unido_en');
    }

    public function miembros()
    {
        return $this->hasMany(miembros_grupo::class, 'grupo_id');
    }

    public function publicaciones()
    {
        return $this->hasMany(publicaciones_grupo::class, 'grupo_id');
    }

    public function logrosPersonalizados()
    {
        return $this->hasMany(logros_personalizados::class, 'grupo_id');
    }
}

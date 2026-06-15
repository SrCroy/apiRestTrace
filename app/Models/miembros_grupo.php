<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class miembros_grupo extends Model
{
    use HasFactory;

    protected $table = 'miembros_grupo';

    public $timestamps = false;

    protected $fillable = [
        'rol',
        'unido_en',
        'grupo_id',
        'usuario_id',
    ];

    protected function casts(): array
    {
        return [
            'unido_en' => 'datetime',
        ];
    }

    public function grupo()
    {
        return $this->belongsTo(grupos::class, 'grupo_id');
    }

    public function usuario()
    {
        return $this->belongsTo(User::class, 'usuario_id');
    }

}

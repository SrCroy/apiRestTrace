<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class usuarios_logros extends Model
{
    use HasFactory;

    protected $table = 'usuarios_logros';

    public $timestamps = false;

    protected $fillable = [
        'usuario_id',
        'logro_id',
        'obtenido_en',
    ];

    protected function casts(): array
    {
        return [
            'obtenido_en' => 'datetime',
        ];
    }

    public function usuario()
    {
        return $this->belongsTo(User::class, 'usuario_id');
    }

    public function logro()
    {
        return $this->belongsTo(logros::class, 'logro_id');
    }
}

<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class usuarios_logros_personalizados extends Model
{
    use HasFactory;

    protected $table = 'usuarios_logros_personalizados';

    public $timestamps = false;

    protected $fillable = [
        'usuario_id',
        'logro_personalizado_id',
        'otorgado_por',
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

    public function logroPersonalizado()
    {
        return $this->belongsTo(logros_personalizados::class, 'logro_personalizado_id');
    }

    public function otorgador()
    {
        return $this->belongsTo(User::class, 'otorgado_por');
    }
}

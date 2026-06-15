<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class reacciones extends Model
{
    use HasFactory;

    protected $table = 'reacciones';

    public $timestamps = false;

    protected $fillable = [
        'reaccionable_tipo',
        'reaccionable_id',
        'usuario_id',
        'creado_en',
    ];

    protected function casts(): array
    {
        return [
            'creado_en' => 'datetime',
        ];
    }

    public function usuario()
    {
        return $this->belongsTo(User::class, 'usuario_id');
    }

    public function reaccionable()
    {
        return $this->morphTo(__FUNCTION__, 'reaccionable_tipo', 'reaccionable_id');
    }
}

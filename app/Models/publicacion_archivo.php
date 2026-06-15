<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class publicacion_archivo extends Model
{
    use HasFactory;

    protected $table = 'publicacion_archivos';

    public $timestamps = false;

    protected $fillable = [
        'url',
        'tipo',
        'orden',
        'publicacion_id',
    ];

    protected function casts(): array
    {
        return [
            'orden' => 'integer',
        ];
    }

    public function publicacion()
    {
        return $this->belongsTo(publicaciones::class, 'publicacion_id');
    }
}

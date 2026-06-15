<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Database\Factories\UserFactory;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Attributes\Hidden;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;

#[Fillable(['name', 'email', 'password'])]
#[Hidden(['password', 'remember_token'])]
class User extends Authenticatable
{
    /** @use HasFactory<UserFactory> */
    use HasFactory, Notifiable, HasApiTokens;

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */

    protected $fillable = [
        'username',
        'nombre',
        'apellido',
        'email',
        'password',
        'avatar',
        'rol',
        'esta_baneado',
        'estado',
        'biografia',
        'peso_kg',
        'altura_cm',
        'fecha_nacimiento',
    ];

    protected $hidden = [
        'password',
        'remember_token',
    ];

    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
        ];
    }
    
    public function rutas(){
         return $this->hasMany(rutas::class, 'usuario_id');
    }

    public function actividades()
    {
        return $this->hasMany(actividades::class, 'id_usuario');
    }

    public function logros()
    {
        return $this->hasMany(usuarios_logros::class, 'usuario_id');
    }

    public function gruposPropietario()
    {
        return $this->hasMany(grupos::class, 'propietario_id');
    }

    public function membresias()
    {
        return $this->hasMany(miembros_grupo::class, 'usuario_id');
    }

    public function publicaciones()
    {
        return $this->hasMany(publicaciones::class, 'usuario_id');
    }

    public function publicacionesGrupo()
    {
        return $this->hasMany(publicaciones_grupo::class, 'usuario_id');
    }

    public function solicitudesEnviadas()
    {
        return $this->hasMany(amistades::class, 'solicitante_id');
    }

    public function solicitudesRecibidas()
    {
        return $this->hasMany(amistades::class, 'receptor_id');
    }

    public function conversacionesComoUno()
    {
        return $this->hasMany(conversaciones::class, 'usuario_uno_id');
    }

    public function conversacionesComoDos()
    {
        return $this->hasMany(conversaciones::class, 'usuario_dos_id');
    }

    public function mensajes()
    {
        return $this->hasMany(mensaje::class, 'remitente_id');
    }

    public function reportesEnviados()
    {
        return $this->hasMany(reporte::class, 'reportador_id');
    }

    public function reportesRevisados()
    {
        return $this->hasMany(reporte::class, 'revisado_por');
    }

    public function reacciones()
    {
        return $this->hasMany(reacciones::class, 'usuario_id');
    }

    public function comentarios()
    {
        return $this->hasMany(comentarios::class, 'usuario_id');
    }

    public function logrosPersonalizadosPropuestos()
    {
        return $this->hasMany(logros_personalizados::class, 'propuesto_por');
    }

    public function logrosPersonalizadosGanados()
    {
        return $this->hasMany(usuarios_logros_personalizados::class, 'usuario_id');
    }
}

<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Spatie\Permission\Traits\HasRoles;   

class User extends Authenticatable
{
	 protected $connection = 'mysql';
    /** @use HasFactory<\Database\Factories\UserFactory> */
	 use HasRoles; 
    use HasFactory, Notifiable;
	 protected $table = 'usuarios';
	 
	  protected $casts = [
        'db' => 'string',
    ];
    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'nombre',
        'apellidos',
        'email',
        'nivel',
        'tipo_identificacion',
        'numero_identificacion',
        'direccion',
        'whatsapp',
        'ciudad',
        'password',
        'club_id',
        'role',
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var array<int, string>
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
        ];
    }
	// Aquí definimos la relación "item" (o como prefieras nombrarla):
    public function club()
    {
        // 'producto' es la FK en 'ventas' que apunta a 'id' de 'items'
        return $this->belongsTo(\App\Models\Club::class, 'club_id', 'id');
    }
}

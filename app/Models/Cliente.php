<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany; 

use Illuminate\Notifications\Notifiable;   

use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\DB;
  

class Cliente extends Model
{
	   protected $connection = 'tenant';
	   
	   public function resolveRouteBinding($value, $field = null)
    {
        // 1) Ajusta la conexión tenant según el usuario autenticado
        if ($user = Auth::user()) {
            $dbName = $user->peluqueria->db;                               // el nombre dynamic de la BD
            Config::set('database.connections.tenant.database', $dbName);
            DB::purge('tenant');
            DB::reconnect('tenant');
        }

        // 2) Resuelve el modelo usando esa conexión
        $field = $field ?: $this->getRouteKeyName();
        return $this->on('tenant')
                    ->where($field, $value)
                    ->firstOrFail();
    }

    protected $fillable = [
  'tipo_identificacion',
  'numero_identificacion',
  'nombres',
  'apellidos',
  'correo',
  'whatsapp',
  'fecha_nacimiento',
  'direccion',
  'pais',
  'departamento',
  'municipio',
  'sexo',
  'nivel_id',
  'tipo',
  'foto',
];

 use Notifiable; 
/**
     * Nivel académico del cliente.
     * La columna 'nivel' en clientes es la FK hacia la tabla niveles.id
     */
    public function nivel()
    {
        // 1er arg: Modelo relacionado
        // 2º arg: foreign key en esta tabla
        // 3er arg: primary key en la tabla de niveles (opcional porque es 'id')
        return $this->belongsTo(Nivel::class, 'nivel_id', 'id');
    }

   

    public function pais()
    {
        return $this->belongsTo(Paises::class, 'pais');
    }

    public function departamento()
    {
        return $this->belongsTo(Departamentos::class, 'departamento');
    }

    public function municipio()
    {
        return $this->belongsTo(Municipios::class, 'municipio');
    }
    /**
     * Relación inversa many-to-many con Clase.
     */
      public function clases(): BelongsToMany
    {
		 return $this->belongsToMany(
        Reserva::class,
        'clase_cliente', // pivote
        'cliente_id',    // FK a clientes en la pivote
        'reserva_id'    // FK a reservas en la pivote
    )->withTimestamps();
		
		
        
    }
	public function reservas()
{
    return $this->hasMany(Reserva::class);
}

	
	public function membresias()
{
    return $this->belongsToMany(
            Membresia::class,
            'membresia_cliente',
            'cliente_id',
            'membresia_id'
        )
        ->using(MembresiaCliente::class)
        ->withPivot('clases','reservas')
        ->withTimestamps();
}

public function membresiasClientes()
{
    return $this->hasMany(MembresiaCliente::class);
}

                      // ← añade el trait

    /** WhatsApp (1MSG) – devuelve el teléfono en formato E.164 */
    public function routeNotificationForOnemsg(): ?string
    {
        return $this->whatsapp;            // Ej. +573001234567
    }




}

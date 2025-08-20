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
  'tipo',
  'foto',
];

 use Notifiable; 


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
   
	public function reservas()
{
    return $this->hasMany(Reserva::class);
}

	


                      // ← añade el trait

    /** WhatsApp (1MSG) – devuelve el teléfono en formato E.164 */
    public function routeNotificationForOnemsg(): ?string
    {
        return $this->whatsapp;            // Ej. +573001234567
    }




}

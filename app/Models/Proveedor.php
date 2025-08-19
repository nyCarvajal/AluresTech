<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Notifications\Notifiable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\DB;

class Proveedor extends Model
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
	use HasFactory, Notifiable;
	 protected $table = 'proveedores';
    
	
    protected $fillable = [
        'tipo_documento_id', 'numero_documento', 'nombre',
        'regimen', 'responsable_iva', 'direccion', 'municipio_id'
    ];

    public function tipoIdentificacion()
    {
        return $this->belongsTo(TipoIdentificacion::class, 'tipo_documento_id');
    }

    public function municipio()
    {
        return $this->belongsTo(Municipios::class, 'municipio_id');
    }

    // Accesor para obtener departamento desde municipio
    public function departamento()
    {
        return $this->municipio->departamento();
    }
}


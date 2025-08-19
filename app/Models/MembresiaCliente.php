<?php

// app/Models/MembresiaCliente.php

namespace App\Models;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\DB;

use Illuminate\Database\Eloquent\Relations\Pivot;

class MembresiaCliente extends Pivot
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
    protected $table = 'membresia_cliente';
    protected $fillable = ['cliente_id','membresia_id','clases','reservas', 'estado', 'numReservas', 'clasesVIstas'];
	
	public function paquete()
    {
        return $this->belongsTo(
            \App\Models\Membresia::class,
            'membresia_id'   // columna que apunta a membresia.id
        );
    }
	
	// app/Models/MembresiaCliente.php
public function cliente()
{
    return $this->belongsTo(\App\Models\Cliente::class, 'cliente_id');
}
}

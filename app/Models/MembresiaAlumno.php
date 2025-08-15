<?php

// app/Models/MembresiaAlumno.php

namespace App\Models;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\DB;

use Illuminate\Database\Eloquent\Relations\Pivot;

class MembresiaAlumno extends Pivot
{
	   protected $connection = 'tenant';
	   public function resolveRouteBinding($value, $field = null)
    {
        // 1) Ajusta la conexión tenant según el usuario autenticado
        if ($user = Auth::user()) {
            $dbName = $user->club->db;                               // el nombre dynamic de la BD
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
    protected $table = 'membresia_alumno';
    protected $fillable = ['alumno_id','membresia_id','clases','reservas', 'estado', 'numReservas', 'clasesVIstas'];
	
	public function paquete()
    {
        return $this->belongsTo(
            \App\Models\Membresia::class,
            'membresia_id'   // columna que apunta a membresia.id
        );
    }
	
	// app/Models/MembresiaAlumno.php
public function alumno()
{
    return $this->belongsTo(\App\Models\Alumno::class, 'alumno_id');
}
}

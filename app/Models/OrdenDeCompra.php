<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\DB;


class OrdenDeCompra extends Model
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
    protected $table = 'orden_de_compras';

    protected $fillable = [
        'fecha_hora',
        'responsable',
        'cliente',
        'activa',
    ];

    protected $casts = [
        'fecha_hora' => 'datetime',
        'activa'     => 'boolean',
    ];
	
	// Aquí definimos la relación "item" (o como prefieras nombrarla):
    public function alumno()
    {
        // 'producto' es la FK en 'ventas' que apunta a 'id' de 'items'
        return $this->belongsTo(\App\Models\Alumno::class, 'cliente', 'id');
    }
	
	// 1. Una orden puede tener muchas ventas
    public function ventas()
    {
        // ‘cuenta’ o ‘cuenta_id’ según tu FK en ventas
        return $this->hasMany(\App\Models\Venta::class, 'cuenta', 'id');
    }
	
	// 1. Una orden puede tener muchas ventas
    public function pagos()
    {
        // ‘cuenta’ o ‘cuenta_id’ según tu FK en ventas
        return $this->hasMany(\App\Models\Pago::class, 'cuenta', 'id');
    }

  
}

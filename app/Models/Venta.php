<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\DB;

class Venta extends Model
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
        'cuenta',
        'producto',
        'cantidad',
        'descuento',
        'valor_unitario',
        'valor_total',
		'porcentaje_comision',
        'usuario_id',
    ];
	
	 // Aquí definimos la relación "item" (o como prefieras nombrarla):
    public function item()
    {
        // 'producto' es la FK en 'ventas' que apunta a 'id' de 'items'
        return $this->belongsTo(\App\Models\Item::class, 'producto', 'id');
    }
	 public function orden()
    {
        return $this->belongsTo(\App\Models\OrdenDeCompra::class, 'cuenta');
    }
        public function usuarioComision()
    {
        return $this->belongsTo(Usuario::class, 'usuario_id');
    }

        public function barbero()
    {
        return $this->belongsTo(Usuario::class, 'usuario_id');
    }
}

<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\DB;

class Salida extends Model
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
        'concepto',
        'fecha',
        'cuenta_bancaria_id',
        'valor',
        'observaciones',
        'responsable_id',
        'tercero_id',
    ];

    protected $casts = [
        'fecha' => 'datetime',
    ];

    public function responsable()
    {
        return $this->belongsTo(User::class, 'responsable_id');
    }

    public function cuentaBancaria()
    {
        return $this->belongsTo(Banco::class, 'cuenta_bancaria_id');
    }

    public function tercero()
    {
        return $this->belongsTo(Proveedor::class, 'tercero_id');
    }
}
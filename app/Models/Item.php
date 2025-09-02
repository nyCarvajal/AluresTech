<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\DB;
use App\Models\InventarioHistorial;

class Item extends Model
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
    use HasFactory;

    protected $table = 'items';
	

    /**
     * Atributos asignables en masa.
     */
    protected $fillable = [
        'nombre',
        'cantidad',
        'costo',
        'valor',
        'tipo',
        'area',
    ];

    public function movimientos()
    {
        return $this->hasMany(InventarioHistorial::class);
    }
}

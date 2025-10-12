<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\DB;
use App\Models\Banco;
use App\Models\User;

class Pago extends Model
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

    // protected $table = 'pagos';

    protected $fillable = [
        'fecha_hora',
        'valor',
        'cuenta',
        'estado',
        'banco',
    ];

    protected $casts = [
        'fecha_hora' => 'datetime',
        'valor'      => 'integer',
    ];

    public function bancoModel()
    {
        return $this->belongsTo(Banco::class, 'banco');
    }

        // app/Models/Pago.php
public function ordenDeCompra()
{
    return $this->belongsTo(OrdenDeCompra::class, 'cuenta'); // ajusta FK si difiere
}

    public function responsableUsuario()
    {
        return $this->belongsTo(User::class, 'responsable');
    }

}

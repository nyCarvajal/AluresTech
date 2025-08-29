<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\DB;

class InventarioHistorial extends Model
{
    use HasFactory;

    protected $connection = 'tenant';
    protected $table = 'inventario_historial';

    protected $fillable = [
        'item_id',
        'cambio',
        'descripcion',
    ];

    protected static function booted()
    {
        // Ajustar conexión tenant según el usuario autenticado
        if ($user = Auth::user()) {
            $dbName = $user->peluqueria->db;
            Config::set('database.connections.tenant.database', $dbName);
            DB::purge('tenant');
            DB::reconnect('tenant');
        }
    }

    public function item()
    {
        return $this->belongsTo(Item::class);
    }
}

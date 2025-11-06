<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\DB;

class Usuario extends Model
{
    protected $connection = 'tenant';

    protected $table = 'usuarios';

    protected $fillable = [
        'nombre',
        'apellidos',
        'peluqueria_id',
        'role',
        'email',
        'nivel',
        'tipo_identificacion',
        'numero_identificacion',
        'direccion',
        'whatsapp',
        'ciudad',
        'color',
    ];

    public function resolveRouteBinding($value, $field = null)
    {
        if ($user = Auth::user()) {
            $dbName = $user->peluqueria->db ?? null;

            if ($dbName) {
                Config::set('database.connections.tenant.database', $dbName);
                DB::purge('tenant');
                DB::reconnect('tenant');
            }
        }

        $field = $field ?: $this->getRouteKeyName();

        return $this->on('tenant')
            ->where($field, $value)
            ->firstOrFail();
    }

    public function getNombreCompletoAttribute(): string
    {
        return trim($this->nombre . ' ' . ($this->apellidos ?? ''));
    }
}

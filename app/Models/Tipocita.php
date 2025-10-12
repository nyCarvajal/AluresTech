<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\DB;

class Tipocita extends Model
{
    protected $connection = 'tenant';
    protected $table = 'tipocita';
    public $timestamps = false;

    protected $fillable = [
        'nombre',
    ];
    public function resolveRouteBinding($value, $field = null)
    {
        if ($user = Auth::user()) {
            $dbName = $user->peluqueria->db;
            Config::set('database.connections.tenant.database', $dbName);
            DB::purge('tenant');
            DB::reconnect('tenant');
        }

        $field = $field ?: $this->getRouteKeyName();

        return $this->on('tenant')
            ->where($field, $value)
            ->firstOrFail();
    }
}


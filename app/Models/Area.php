<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\DB;

class Area extends Model
{
    protected $connection = 'tenant';

    protected $table = 'areas';

    protected $fillable = [
        'descripcion',
    ];

    public $timestamps = false;

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

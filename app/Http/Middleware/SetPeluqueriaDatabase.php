<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class SetPeluqueriaDatabase
{
    public function handle(Request $request, Closure $next)
    {
        if ($peluqueria = $request->user()->peluqueria) {
            config(['database.connections.tenant.database' => $peluqueria->db]);
            DB::purge('tenant');
            DB::reconnect('tenant');
            // ¡y opcionalmente puedes forzar tenant como default!
          //  config(['database.default' => 'tenant']);

        }

        return $next($request);
    }
}

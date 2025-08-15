<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class SetClubDatabase
{
    public function handle(Request $request, Closure $next)
    {
        if ($club = $request->user()->club) {
            config(['database.connections.tenant.database' => $club->db]);
            DB::purge('tenant');
            DB::reconnect('tenant');
            // ¡y opcionalmente puedes forzar tenant como default!
          //  config(['database.default' => 'tenant']);

        }

        return $next($request);
    }
}

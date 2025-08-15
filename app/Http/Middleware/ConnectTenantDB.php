<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class ConnectTenantDB
{
    public function handle($request, Closure $next)
    {
	
	//dd( Auth::user());
        if ($user = Auth::user()) {
            
			 $database = $user->club->db;

            // 1) Inyecta en la conexión tenant
            config(['database.connections.tenant.database' => $database]);
            config(['database.default' => 'tenant']); // si quieres que 'tenant' sea default

            // 2) Purga y reconecta
            DB::purge('tenant');
            DB::reconnect('tenant');

            // 3) FORZAR el USE explícito
          DB::setDefaultConnection('tenant');
			
			    $current = DB::connection('tenant')->getDatabaseName();
       // dd("Auth user: {$user->id}", "Inyecté: {$database}", "Conexión tenant usa: {$current}");
  
        }

        return $next($request);
    }
}

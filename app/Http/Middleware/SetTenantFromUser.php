<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Stancl\Tenancy\Tenancy;

class SetTenantFromUser
{
    protected Tenancy $tenancy;

    public function __construct(Tenancy $tenancy)
    {
        $this->tenancy = $tenancy;
    }

    public function handle($request, Closure $next)
    {
        // 1) Solo si hay usuario autenticado
        if ($user = Auth::user()) {
            $tenantId = $user->peluqueria_id;

            // 2) Inicializa el tenant
            $this->tenancy->initialize($tenantId);

            // 3) Obtén el modelo Tenant
            $tenant = tenancy()->tenant;
            if (! $tenant) {
                abort(404, "No se encontró tenant con ID={$tenantId}");
            }
			
            // 4) Saca el nombre de BD del JSON
            $dbName = $tenant->database;
            if (! $dbName) {
                dd("El tenant ID={$tenantId} no tiene 'database' en data");
            }

            // 5) Inyecta y reconecta
            config(['database.connections.tenant.database' => $dbName]);
            DB::purge('tenant');
            DB::reconnect('tenant');

            // ¡Comprueba que ahora exista!
            dd("Tenancy cargado correctamente: {$dbName}");
        }

        return $next($request);
    }
}

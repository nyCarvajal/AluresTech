<?php

namespace App\Http\Controllers;

use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Foundation\Bus\DispatchesJobs;
use Illuminate\Foundation\Validation\ValidatesRequests;
use Illuminate\Routing\Controller as BaseController;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;


class Controller extends BaseController
{
    use AuthorizesRequests, ValidatesRequests;
	
	public function __construct()
    {
        // Este closure se ejecuta antes de cada método de cualquier controlador hijo
        $this->middleware(function ($request, $next) {
            if ($user = Auth::user()) {
                $database = $user->club->db;

                // Inyecta la base tenant y la hace default
                config(['database.connections.tenant.database' => $database]);
                DB::purge('tenant');
                DB::reconnect('tenant');
                DB::setDefaultConnection('tenant');
            }

            return $next($request);
        });
    }
}

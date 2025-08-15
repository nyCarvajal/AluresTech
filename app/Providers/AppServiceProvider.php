<?php

// app/Providers/AppServiceProvider.php
namespace App\Providers;
use Illuminate\Foundation\Support\Providers\RouteServiceProvider as ServiceProvider;

use Illuminate\Routing\Events\RouteMatched;
use Illuminate\Support\Facades\Event;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;

class AppServiceProvider extends ServiceProvider
{
    public function boot()
    {
        Event::listen(RouteMatched::class, function (RouteMatched $event) {
            if ($user = Auth::user()) {
                $db = $user->club->db;
                Config::set('database.connections.tenant.database', $db);
                DB::purge('tenant');
                DB::reconnect('tenant');
                DB::setDefaultConnection('tenant');
            }
        });
    }
}


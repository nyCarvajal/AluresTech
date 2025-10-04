<?php

// app/Providers/AppServiceProvider.php
namespace App\Providers;
use Illuminate\Foundation\Support\Providers\RouteServiceProvider as ServiceProvider;

use Illuminate\Routing\Events\RouteMatched;
use Illuminate\Support\Facades\Event;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\View;
use App\Models\Reserva;

class AppServiceProvider extends ServiceProvider
{
    public function boot()
    {
        Event::listen(RouteMatched::class, function (RouteMatched $event) {
            if ($user = Auth::user()) {
                $db = $user->peluqueria->db;
                Config::set('database.connections.tenant.database', $db);
                DB::purge('tenant');
                DB::reconnect('tenant');
                DB::setDefaultConnection('tenant');
            }
        });

        View::composer('layouts.app', function ($view) {
            $count = 0;

            if ($user = Auth::user()) {
                $peluqueria = $user->peluqueria;

                if ($peluqueria && $peluqueria->db) {
                    Config::set('database.connections.tenant.database', $peluqueria->db);
                    DB::purge('tenant');
                    DB::reconnect('tenant');
                    DB::setDefaultConnection('tenant');

                    $count = Reserva::where('estado', 'Pendiente')->count();
                }
            }

            $view->with('pendingReservationsCount', $count);
        });
    }
}


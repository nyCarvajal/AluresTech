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
use App\Models\Cliente;
use Carbon\Carbon;

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

        View::composer(['layouts.app', 'layouts.vertical', 'layouts.partials.topbar'], function ($view) {
            static $count = null;
            static $birthdayCount = null;

            if ($count === null) {
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
            }

            if ($birthdayCount === null) {
                $birthdayCount = 0;

                if ($user = Auth::user()) {
                    $peluqueria = $user->peluqueria;

                    if ($peluqueria && $peluqueria->db) {
                        Config::set('database.connections.tenant.database', $peluqueria->db);
                        DB::purge('tenant');
                        DB::reconnect('tenant');
                        DB::setDefaultConnection('tenant');

                        $today = Carbon::today();

                        $birthdayCount = Cliente::whereNotNull('fecha_nacimiento')
                            ->whereMonth('fecha_nacimiento', $today->month)
                            ->whereDay('fecha_nacimiento', $today->day)
                            ->count();
                    }
                }
            }

            $view->with('pendingReservationsCount', $count);
            $view->with('todayBirthdayCount', $birthdayCount);
        });
    }
}


<?php

namespace App\Http;

use Illuminate\Foundation\Http\Kernel as HttpKernel;

class Kernel extends HttpKernel
{
    /**
     * Middlewares globales a todas las peticiones HTTP.
     */
    protected $middleware = [
        \App\Http\Middleware\TrustProxies::class,
        \Illuminate\Http\Middleware\HandleCors::class,
        \App\Http\Middleware\PreventRequestsDuringMaintenance::class,
        \Illuminate\Foundation\Http\Middleware\ValidatePostSize::class,
        \App\Http\Middleware\TrimStrings::class,
        \Illuminate\Foundation\Http\Middleware\ConvertEmptyStringsToNull::class,
    ];

    /**
     * Grupos de middleware para rutas.
     */
    protected $middlewareGroups = [
        'web' => [
            // 1) Encripta y añade cookies
            \App\Http\Middleware\EncryptCookies::class,
            \Illuminate\Cookie\Middleware\AddQueuedCookiesToResponse::class,

            // 2) Arranca la sesión
            \Illuminate\Session\Middleware\StartSession::class,

            // 3) Comparte errores de validación vía session flash
            \Illuminate\View\Middleware\ShareErrorsFromSession::class,

            // 4) Verifica el CSRF token
            \App\Http\Middleware\VerifyCsrfToken::class,

            // 5) Sustituye {bindings} en las rutas
            \Illuminate\Routing\Middleware\SubstituteBindings::class,

            // 6) Autentica al usuario
            \Illuminate\Auth\Middleware\Authenticate::class,

            // 7) Conecta la base de datos tenant
            \App\Http\Middleware\ConnectTenantDB::class,
        ],

        'api' => [
            'throttle:api',
            \Illuminate\Routing\Middleware\SubstituteBindings::class,
        ],
    ];

    /**
     * Alias para middleware que puedes usar en rutas individuales.
     */
    protected $routeMiddleware = [
        'auth'      => \Illuminate\Auth\Middleware\Authenticate::class,
        'verified'  => \Illuminate\Auth\Middleware\EnsureEmailIsVerified::class,
        'role'      => \Spatie\Permission\Middlewares\RoleMiddleware::class,
        'permission'=> \Spatie\Permission\Middlewares\PermissionMiddleware::class,
        'role_or_permission' => \Spatie\Permission\Middlewares\RoleOrPermissionMiddleware::class,
    ];

    /**
     * Prioridad de ejecución de middleware.
     */
    protected $middlewarePriority = [
        \Illuminate\Session\Middleware\StartSession::class,
        \Illuminate\View\Middleware\ShareErrorsFromSession::class,
        \Illuminate\Routing\Middleware\SubstituteBindings::class,
        \Illuminate\Auth\Middleware\Authenticate::class,
        \App\Http\Middleware\ConnectTenantDB::class,
    ];
}

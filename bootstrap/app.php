<?php
use App\Http\Middleware\ConnectTenantDB;

use Illuminate\Foundation\Configuration\Middleware as MiddlewareConfigurator;
use Illuminate\Foundation\Application;
use Illuminate\Foundation\Configuration\Exceptions;

return Application::configure(basePath: dirname(__DIR__))
    ->withRouting(
        web: __DIR__.'/../routes/web.php',
        commands: __DIR__.'/../routes/console.php',
        health: '/up',
    )
    ->withMiddleware(function (MiddlewareConfigurator $middleware) {
    // Esto añade tu middleware al final del stack global
	
		$middleware->append(ConnectTenantDB::class);
})
    ->withExceptions(function (Exceptions $exceptions) {
        //
    })->create();

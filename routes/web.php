<?php

use App\Http\Controllers\RoutingController;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AlumnosController;
use App\Http\Controllers\ClaseController;
use App\Http\Controllers\CanchaController;

use App\Http\Controllers\DeporteController;
use App\Http\Controllers\CajaController;
use App\Http\Controllers\ClubController;
use App\Http\Controllers\MembresiaController;
use App\Http\Controllers\PagoController;
use App\Http\Controllers\OrdendecompraController;
use App\Http\Controllers\ReservaController;
use App\Http\Controllers\VentaController;
use App\Http\Controllers\RecordatorioController;
use App\Http\Controllers\Api\LocationController;
use App\Http\Controllers\NivelController;
use App\Http\Controllers\UsuarioController;
use App\Http\Controllers\BancoController;
use App\Http\Controllers\ItemController;
use App\Http\Controllers\TipoUsuarioController;
use App\Http\Controllers\SalidaController;
use App\Http\Controllers\ProveedorController;
use App\Http\Controllers\Admin\DashboardController;
use App\Http\Controllers\Auth\LoginController;
use App\Http\Middleware\ConnectTenantDB;
use App\Http\Controllers\Auth\AuthenticatedSessionController;

use Illuminate\Cookie\Middleware\EncryptCookies;
use Illuminate\Cookie\Middleware\AddQueuedCookiesToResponse;
use Illuminate\Session\Middleware\StartSession;
use Illuminate\View\Middleware\ShareErrorsFromSession;
use Illuminate\Foundation\Http\Middleware\VerifyCsrfToken;
use Illuminate\Routing\Middleware\SubstituteBindings;
use Illuminate\Auth\Middleware\Authenticate;


require __DIR__ . '/auth.php';

// Rutas públicas
Route::get('login',  [LoginController::class, 'showLoginForm'])->name('login');
Route::post('login', [LoginController::class, 'login']);
Route::get('/auth/signin', [LoginController::class, 'showLoginForm'])
     ->name('auth.showLoginForm');

// Rutas protegidas
//Route::middleware('auth')->group(function () {
  //  Route::post('logout', [LoginController::class, 'logout'])->name('logout');
// });


Route::post('/logout', [AuthenticatedSessionController::class, 'destroy'])
     ->middleware('auth')
     ->name('logout');


Route::middleware([
        EncryptCookies::class,
        AddQueuedCookiesToResponse::class,
        StartSession::class,
        ShareErrorsFromSession::class,
        VerifyCsrfToken::class,
        SubstituteBindings::class,
        Authenticate::class,           // 1️⃣ primero autentica
        ConnectTenantDB::class, // 2️⃣ luego conecta tenant
		
    ])
	 ->group(function () {
		 
		 
Route::get('/ordenes/{orden}/pdf', [OrdendecompraController::class, 'pdf'])->name('ordenes.pdf');
Route::post('/ordenes/{orden}/email', [OrdendecompraController::class, 'sendEmail'])->name('ordenes.email');
	

Route::resource('proveedores', ProveedorController::class);

Route::resource('salidas', SalidaController::class);
		 
	Route::get('/dashboard', [DashboardController::class, 'index'])
    ->name('dashboard');
	
	Route::resource('cajas', CajaController::class);

    Route::get('users/entrenadores/create', 
              [UsuarioController::class, 'createTrainer'])
         ->name('users.trainers.create');

Route::post('users/entrenadores', 
               [UsuarioController::class, 'storeTrainer'])
         ->name('users.trainers.store');

Route::get('users/administradores/create', 
              [UsuarioController::class, 'createAdmin'])
         ->name('users.admins.create');
Route::post('users/administradores', 
               [UsuarioController::class, 'storeAdmin'])
         ->name('users.admins.store');
Route::get('users', [UsuarioController::class,'index'])
     ->name('users.index');
Route::get('users/{user}/edit', [UsuarioController::class,'edit'])
     ->name('users.edit');
Route::put('users/{user}', [UsuarioController::class,'update'])
     ->name('users.update');
Route::delete('users/{user}', [UsuarioController::class,'destroy'])
     ->name('users.destroy');
Route::get('reservas/horario', [ReservaController::class, 'horario'])->name('reservas.horario');
Route::resource('proveedores', ProveedorController::class);
Route::resource('salidas', SalidaController::class);
Route::get('/alumnosb', [AlumnosController::class, 'search'])->name('alumnos.search');
Route::resource('tipo-usuarios', TipoUsuarioController::class);
Route::resource('clubes', ClubController::class)
     ->except(['edit','update']);
Route::get('club/editar',     [ClubController::class, 'editOwn'])
         ->name('clubes.edit');
Route::put('club/editar',     [ClubController::class, 'updateOwn'])
         ->name('clubes.update');
Route::get('club/perfil', [ClubController::class,'showOwn'])
     ->name('clubes.perfil');
Route::post('/ventas/storememb', [VentaController::class, 'storeMemb'])
     ->name('ventas.storememb');
Route::get('/ventas/relacion', [VentaController::class, 'relacion'])->name('ventas.relacion');
Route::get('/', function () {
    return redirect()->route('items.index');
});
Route::resource('items', ItemController::class);
Route::get('/calendar', [ReservaController::class, 'calendar'])->name('reservas.calendar');
Route::get('/reservas.json', [ReservaController::class, 'events'])
     ->name('reservas.events');
Route::post('reservas/{reserva}/cobrar', [ReservaController::class, 'cobrar'])
     ->name('reservas.cobrar');
Route::resource('reservas', ReservaController::class);
Route::resource('clases',  ClaseController::class);
//	Route::resource('torneos', TorneoController::class);
Route::get('reservas.json', [ReservaController::class, 'events'])->name('reservas.events');
Route::resource('niveles', NivelController::class)->except(['create', 'edit', 'show']);
Route::resource('membresias', MembresiaController::class)->except(['create', 'edit', 'show']);
Route::resource('canchas', CanchaController::class);
    Route::resource('deportes', DeporteController::class);
    Route::resource('nivels', NivelController::class);
	Route::resource('usuario', UsuarioController::class);
	Route::resource('clase', ClaseController::class);
	Route::resource('alumnos', AlumnosController::class);
	Route::resource('caja', CajaController::class);
	Route::resource('club', ClubController::class);
	Route::resource('membresias', MembresiaController::class);
	Route::resource('pagos', PagoController::class);
	Route::resource('orden_de_compras', OrdendecompraController::class);
	Route::resource('recordatorio', RecordatorioController::class);
	Route::resource('reservas', ReservaController::class);
	Route::resource('ventas', VentaController::class);
Route::get('/ventas', [VentaController::class, 'index'])->name('ventas.index');
Route::post('/ventas/por-item', [VentaController::class, 'storeByItem'])
     ->name('ventas.storeByItem');
Route::get('/', function () {
    return redirect()->route('bancos.index');
});
Route::resource('bancos', BancoController::class);
Route::get('/pagos/cuenta/{cuenta}', [PagoController::class, 'porCuenta'])
     ->name('pagos.porCuenta');
	 
	 // routes/web.php
Route::middleware(['auth'])
      ->resource('membresia-alumno', \App\Http\Controllers\MembresiaAlumnoController::class)
      ->only(['edit','update']);    // solo las que necesitamos

	 
	 });
	 
	 
	 
Route::get('reserva/availability', [ReservaController::class, 'availability']);
Route::get('/', [AlumnosController::class, 'create']);
Route::get('/departamentos', [LocationController::class, 'departamentos']);
	Route::get('/municipios',    [LocationController::class, 'municipios']);
	
Route::match(['GET','POST'], 'webhook/whatsapp',
    \App\Http\Controllers\WhatsappWebhookController::class);
	



 Route::get('', [RoutingController::class, 'index'])->name('root');
    Route::get('{first}/{second}/{third}', [RoutingController::class, 'thirdLevel'])->name('third');
    Route::get('{first}/{second}', [RoutingController::class, 'secondLevel'])->name('second');
    Route::get('{any}', [RoutingController::class, 'root'])->name('any');


	

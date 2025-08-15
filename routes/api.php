<?php
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\ReservaController;
use App\Http\Controllers\AlumnoController;

Route::get('reserva/availability', [ReservaController::class, 'availability']);
// routes/api.php  (o web.php si lo prefieres)
Route::get('/alumnosb', [AlumnoController::class, 'search'])->name('alumnos.search');


?>

<?php
// app/Http/Controllers/Admin/DashboardController.php
namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Cliente;
use App\Models\Pago;
use App\Models\Reserva;
use App\Models\OrdenDeCompra; 
use Illuminate\Support\Facades\Auth;
use Illuminate\Http\Request;
use App\Http\Middleware\ConnectTenantDB;

class DashboardController extends Controller
{
	
     public function index()
    {
       // 1) Verificamos sesión
	  
        if (!Auth::check()) {
            return redirect()->route('auth.signin');   // o la que sea tu ruta de login
        }

        // 2) Cargamos métricas y tablas ============================
        $totalClientes = Cliente::whereYear('created_at', now()->year)
                         ->whereMonth('created_at', now()->month)
                         ->count();
        $totalPagosMes = Pago::whereYear('fecha_hora', now()->year)   // o el nombre de tu timestamp
                    ->whereMonth('fecha_hora', now()->month)
					->whereHas('ordenDeCompra', function ($q) {
						$q->where('activa', 1);   // o 'activo' según tu columna
    })
                    ->sum('valor'); 
		
		$totalReservas=Reserva::where('tipo', 'Reserva')        // ó 'tipo', según tu columna
    ->where('estado', '!=', 'Cancelada') 
	->whereYear('fecha', now()->year)     // campo fecha/timestamp de la reserva
    ->whereMonth('fecha', now()->month)
    ->count();
		$totalClases=Reserva::where('tipo', 'Clase')        // ó 'tipo', según tu columna
     ->where('estado', '!=', 'Cancelada') 
	->whereYear('fecha', now()->year)     // campo fecha/timestamp de la reserva
    ->whereMonth('fecha', now()->month)
    ->count();
        // últimos 10 (cambia a ->paginate(10) si quieres paginación)
        $clientes = Cliente::latest()->take(10)->get();
        $cuentas =  OrdenDeCompra::with('clienterel')  
		->whereYear('fecha_hora', now()->year)       // ← año actual
        ->whereMonth('fecha_hora', now()->month) 
		->whereNotNull('cliente')          
		->where('activa', 1)// columna llena
		->whereHas('clienterel')  		// eager-load del cliente
        ->withSum('ventas as monto', 'valor_total')  // suma de sus ventas
        ->latest('fecha_hora')                       // orden cronológico
        ->take(10)
        ->get(['id','fecha_hora']);  
		
		

        // 3) Enviamos directamente a la vista
        //    ──> ¡Ya no redirigimos!
        return view('admin.index', compact(
            'totalReservas', 'totalClases', 'totalClientes', 'totalPagosMes', 'clientes', 'cuentas'
        ));
    }
}

<?php

namespace App\Http\Controllers;

use App\Models\Caja;
use App\Models\Pago;
use App\Models\Salida;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;

class CajaController extends Controller
{
    /** Listado ---------------------------------------------------------- */
    public function index()
    {
        $cajas = Caja::latest('fecha_hora')->get();

        return view('cajas.index', compact('cajas'));
    }

    /** Formulario de creación ------------------------------------------- */
    public function create()
    {
        return view('cajas.create');
    }

    /** Persistir nueva caja --------------------------------------------- */
    public function store(Request $request)
    {
        $data = $request->validate([
            'fecha_hora' => ['required', 'date'],
            'base'       => ['required', 'numeric', 'min:0'],
            'valor'      => ['nullable', 'numeric', 'min:0'],
        ]);

        // HTML5 datetime-local llega como string → Carbon
        $data['fecha_hora'] = Carbon::parse($data['fecha_hora']);
		 $data['responsable'] = auth()->id();    
        Caja::create($data);

        return redirect()
            ->route('cajas.index')
            ->with('success', 'Caja creada correctamente.');
    }

    /** Mostrar detalle (por si lo necesitas) ---------------------------- */
  public function show(Caja $caja)
{
    // Definimos el rango del mismo día de la caja
    $desde = Carbon::parse($caja->fecha_hora)->startOfDay();
    $hasta = Carbon::parse($caja->fecha_hora)->endOfDay();
	
    // Sumamos pagos y salidas según el rango
    $totalPagos = Pago::whereBetween('fecha_hora', [$desde, $hasta])->sum('valor');
    $totalSalidas = Salida::whereBetween('fecha', [$desde, $hasta])->sum('valor');
	
	$salidas=Salida::whereBetween('fecha', [$desde, $hasta])->get();
	$pagos= Pago::whereBetween('fecha_hora', [$desde, $hasta])->get();
    // Calculamos el total
    $valorCalculado = $caja->base + $totalPagos - $totalSalidas;

    return view('cajas.show', compact('caja', 'totalPagos', 'totalSalidas', 'valorCalculado', 'salidas', 'pagos'));
}

    /** Formulario de edición -------------------------------------------- */
    public function edit(Caja $caja)
    {
        return view('cajas.edit', compact('caja'));
    }

    /** Actualizar -------------------------------------------------------- */
    public function update(Request $request, Caja $caja)
    {
        $data = $request->validate([
            'fecha_hora' => ['required', 'date'],
            'base'       => ['required', 'numeric', 'min:0'],
            'valor'      => ['nullable', 'numeric', 'min:0'],
        ]);

        $data['fecha_hora'] = Carbon::parse($data['fecha_hora']);

        $caja->update($data);

        return redirect()
            ->route('cajas.index')
            ->with('success', 'Caja actualizada correctamente.');
    }

    /** Eliminar (opcional) ---------------------------------------------- */
    public function destroy(Caja $caja)
    {
        $caja->delete();

        return back()->with('success', 'Caja eliminada.');
    }
}

<?php

namespace App\Http\Controllers;

use App\Models\Pago;
use App\Models\Banco;
use App\Models\OrdenDeCompra;
use Carbon\Carbon;
use Illuminate\Http\Request;

class PagoController extends Controller
{
	
	
	/**
     * Mostrar todos los pagos cuyo campo "cuenta" coincida con el parámetro recibido.
     *
     * @param  string  $cuenta
     * @return \Illuminate\View\View
     */
    public function porCuenta($cuenta)
    {
        // Consulta todos los pagos donde 'cuenta' == $cuenta
        $pagos = Pago::where('cuenta', $cuenta)
                     ->orderBy('fecha_hora', 'desc')
                     ->get();

        // Retornamos la vista con la colección de pagos y la cuenta buscada
        return view('pagos.porCuenta', [
            'pagos'  => $pagos,
            'cuenta' => $cuenta,
        ]);
    }
    /**
     * Mostrar lista de pagos.
     */
    public function index()
    {
        $pagos = Pago::with('bancoModel')->orderBy('id', 'desc')->paginate(10);
        return view('pagos.index', compact('pagos'));
    }

    /**
     * Formulario para crear nuevo pago.
     */
    public function create(Request $request)
    {
        $bancos = Banco::all();
        $cuentaId = $request->integer('cuenta');

        $saldoPendiente = null;

        if ($cuentaId) {
            $orden = OrdenDeCompra::with(['ventas', 'pagos'])->find($cuentaId);

            if ($orden) {
                $subtotal = optional($orden->ventas)->sum('valor_total') ?? 0;
                $pagado   = optional($orden->pagos)->sum('valor') ?? 0;

                $saldoPendiente = max(0, $subtotal - $pagado);
            }
        }

        $defaultDate = Carbon::now('America/Bogota')->format('Y-m-d\TH:i');

        return view('pagos.create', compact('bancos', 'saldoPendiente', 'defaultDate'));
    }

  /**
     * Almacena un nuevo pago en la base de datos.
     * Espera recibir:
     *   - venta_id    (la venta que se está pagando)
     *   - fecha_hora  (fecha y hora del pago)
     *   - valor       (monto pagado)
     *   - cuenta      (id de la cuenta/orden asociada)
     *   - estado      (por ejemplo "pendiente" o "completado")
     */
    public function store(Request $request)
    {
        $data = $request->validate([

            'fecha_hora' => 'required|date',
            'valor'      => 'required|integer|min:0',
            'cuenta'     => 'required|integer',            // asumiendo que es orden_id
            
            'banco'      => 'nullable|integer|exists:bancos,id',

        ]);

        // Crear el pago
        Pago::create($data);

        if ($request->boolean('redirect_to_order')) {
            return redirect()
                ->route('orden_de_compras.show', ['orden_de_compra' => $data['cuenta']])
                ->with('success', 'Pago registrado correctamente.');
        }

        return redirect()
            ->back()
            ->with('success', 'Pago registrado correctamente.');
    }


    /**
     * Mostrar un pago.
     */
    public function show(Pago $pago)
    {
        return view('pagos.show', compact('pago'));
    }

    /**
     * Formulario para editar pago.
     */
    public function edit(Pago $pago)
    {
        $bancos = Banco::all();
        return view('pagos.edit', compact('pago', 'bancos'));
    }

    /**
     * Actualizar pago en BD.
     */
    public function update(Request $request, Pago $pago)
    {
        $validated = $request->validate([
            'fecha_hora'=> 'required|date',
            'valor'     => 'required|integer|min:0',
            'estado'    => 'required|integer|in:0,1',
            'banco'     => 'nullable|integer|exists:bancos,id',
            'valor'     => 'required|numeric|min:0',
            'cuenta'    => 'required|string|max:255',
            'estado'    => 'required|string|max:100',
            
        ]);

        $pago->update($validated);

        return redirect()->route('pagos.index')
                         ->with('success', 'Pago actualizado correctamente.');
    }

    /**
     * Eliminar un pago.
     */
    public function destroy(Pago $pago)
    {
        $pago->delete();

        return redirect()->route('pagos.index')
                         ->with('success', 'Pago eliminado correctamente.');
    }
}

<?php

namespace App\Http\Controllers;

use App\Models\MembresiaCliente;
use App\Models\Membresia;           // catálogo de planes
use Illuminate\Http\Request;

class MembresiaClienteController extends Controller
{
    /** Formulario de edición */
    public function edit(MembresiaCliente $membresia_cliente)
    {
        // Al llegar aquí, el resolveRouteBinding del modelo ya
        // cambió la conexión a la BD del peluqueria correspondiente.
        $membresias = Membresia::all();          // planes disponibles
        return view('membresia-cliente.edit', compact('membresia_cliente', 'membresias'));
    }

    /** Guardar cambios */
    public function update(Request $request, MembresiaCliente $membresia_cliente)
    {
        $data = $request->validate([
            'numReservas' =>  ['nullable','integer','min:0'],
			'clases'       => ['nullable','integer','min:0'],
            'clasesVistas' => ['nullable','integer','min:0'],
            'reservas'     => ['nullable','integer','min:0'],
            'estado'       => ['required','in:1,0'],
        ]);

        $membresia_cliente->update($data);

            return redirect()
        ->route('clientes.show', $membresia_cliente->cliente)   // 👈 aquí
        ->with('success', 'Suscripción actualizada correctamente.');

    }
}

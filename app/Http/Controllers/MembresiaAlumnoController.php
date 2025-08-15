<?php

namespace App\Http\Controllers;

use App\Models\MembresiaAlumno;
use App\Models\Membresia;           // catálogo de planes
use Illuminate\Http\Request;

class MembresiaAlumnoController extends Controller
{
    /** Formulario de edición */
    public function edit(MembresiaAlumno $membresia_alumno)
    {
        // Al llegar aquí, el resolveRouteBinding del modelo ya
        // cambió la conexión a la BD del club correspondiente.
        $membresias = Membresia::all();          // planes disponibles
        return view('membresia-alumno.edit', compact('membresia_alumno', 'membresias'));
    }

    /** Guardar cambios */
    public function update(Request $request, MembresiaAlumno $membresia_alumno)
    {
        $data = $request->validate([
            'numReservas' =>  ['nullable','integer','min:0'],
			'clases'       => ['nullable','integer','min:0'],
            'clasesVistas' => ['nullable','integer','min:0'],
            'reservas'     => ['nullable','integer','min:0'],
            'estado'       => ['required','in:1,0'],
        ]);

        $membresia_alumno->update($data);

            return redirect()
        ->route('alumnos.show', $membresia_alumno->alumno)   // 👈 aquí
        ->with('success', 'Suscripción actualizada correctamente.');

    }
}

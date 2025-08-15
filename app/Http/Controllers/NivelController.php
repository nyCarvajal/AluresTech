<?php

namespace App\Http\Controllers;

use App\Models\Nivel;
use Illuminate\Http\Request;

class NivelController extends Controller
{
    /**
     * Mostrar el listado de niveles.
     */
    public function index()
    {
        $niveles = Nivel::orderBy('id', 'asc')->get();
        return view('niveles.index', compact('niveles'));
    }

    /**
     * Guardar un nuevo nivel.
     */
    public function store(Request $request)
    {
        $request->validate([
            'nivel' => 'required|string|max:255',
        ]);

        Nivel::create([
            'nivel' => $request->nivel,
        ]);

        return redirect()->route('niveles.index')
                         ->with('success', 'Nivel creado correctamente.');
    }

    /**
     * Actualizar un nivel existente.
     */
    public function update(Request $request, Nivel $nivel)
    {
        $request->validate([
            'nivel' => 'required|string|max:255',
        ]);

        $nivel->update([
            'nivel' => $request->nivel,
        ]);

        return redirect()->route('niveles.index')
                         ->with('success', 'Nivel actualizado correctamente.');
    }

    /**
     * Eliminar un nivel.
     */
    public function destroy(Nivel $nivel)
    {
        $nivel->delete();
        return redirect()->route('niveles.index')
                         ->with('success', 'Nivel eliminado correctamente.');
    }
}

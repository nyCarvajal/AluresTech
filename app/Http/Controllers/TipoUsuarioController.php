<?php

// app/Http/Controllers/TipoUsuarioController.php

namespace App\Http\Controllers;

use App\Models\TipoUsuario;
use Illuminate\Http\Request;

class TipoUsuarioController extends Controller
{
    /**
     * Mostrar listado de tipos de usuario
     */
    public function index()
    {
        $tipos = TipoUsuario::all();
        return view('tipo-usuarios.index', compact('tipos'));
    }

    /**
     * Formulario para crear un nuevo tipo
     */
    public function create()
    {
        return view('tipo-usuarios.create');
    }

    /**
     * Guardar nuevo tipo en la base de datos
     */
    public function store(Request $request)
    {
        $data = $request->validate([
            'nombre' => 'required|string|max:255',
        ]);

        TipoUsuario::create($data);
        return redirect()->route('tipo-usuarios.index')
                         ->with('success', 'Tipo de usuario creado correctamente.');
    }

    /**
     * Formulario para editar un tipo existente
     */
    public function edit(TipoUsuario $tipoUsuario)
    {
        return view('tipo-usuarios.edit', compact('tipoUsuario'));
    }

    /**
     * Actualizar tipo en la base de datos
     */
    public function update(Request $request, TipoUsuario $tipoUsuario)
    {
        $data = $request->validate([
            'nombre' => 'required|string|max:255',
        ]);

        $tipoUsuario->update($data);
        return redirect()->route('tipo-usuarios.index')
                         ->with('success', 'Tipo de usuario actualizado correctamente.');
    }

    /**
     * Eliminar un tipo de usuario
     */
    public function destroy(TipoUsuario $tipoUsuario)
    {
        $tipoUsuario->delete();
        return redirect()->route('tipo-usuarios.index')
                         ->with('success', 'Tipo de usuario eliminado correctamente.');
    }
}

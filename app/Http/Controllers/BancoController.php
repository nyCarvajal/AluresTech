<?php

namespace App\Http\Controllers;

use App\Models\Banco;
use Illuminate\Http\Request;

class BancoController extends Controller
{
    /**
     * Mostrar todos los bancos.
     */
    public function index()
    {
        // Obtener todos los registros paginados (por ejemplo 10 por página)
        $bancos = Banco::orderBy('id', 'desc')->paginate(10);
        return view('bancos.index', compact('bancos'));
    }

    /**
     * Mostrar formulario para crear un nuevo banco.
     */
    public function create()
    {
        return view('bancos.create');
    }

    /**
     * Almacenar un nuevo banco en la base de datos.
     */
    public function store(Request $request)
    {
        // Validar entrada
        $request->validate([
            'nombre'         => 'required|string|max:255',
            'saldo_inicial'  => 'required|numeric|min:0',
            'saldo_actual'   => 'required|numeric|min:0',
        ]);

        // Crear el registro (mass assignment)
        Banco::create([
            'nombre'        => $request->nombre,
            'saldo_inicial' => $request->saldo_inicial,
            'saldo_actual'  => $request->saldo_actual,
        ]);

        return redirect()
            ->route('bancos.index')
            ->with('success', 'Banco creado correctamente.');
    }

    /**
     * Mostrar un banco en particular.
     */
    public function show(Banco $banco)
    {
        return view('bancos.show', compact('banco'));
    }

    /**
     * Mostrar formulario para editar un banco existente.
     */
    public function edit(Banco $banco)
    {
        return view('bancos.edit', compact('banco'));
    }

    /**
     * Actualizar un banco existente.
     */
    public function update(Request $request, Banco $banco)
    {
        // Validar entrada
        $request->validate([
            'nombre'         => 'required|string|max:255',
            'saldo_inicial'  => 'required|numeric|min:0',
            'saldo_actual'   => 'required|numeric|min:0',
        ]);

        // Actualizar el registro
        $banco->update([
            'nombre'        => $request->nombre,
            'saldo_inicial' => $request->saldo_inicial,
            'saldo_actual'  => $request->saldo_actual,
        ]);

        return redirect()
            ->route('bancos.index')
            ->with('success', 'Banco actualizado correctamente.');
    }

    /**
     * Eliminar un banco.
     */
    public function destroy(Banco $banco)
    {
        $banco->delete();

        return redirect()
            ->route('bancos.index')
            ->with('success', 'Banco eliminado correctamente.');
    }
}

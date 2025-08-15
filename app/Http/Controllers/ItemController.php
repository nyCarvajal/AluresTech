<?php

namespace App\Http\Controllers;

use App\Models\Item;
use Illuminate\Http\Request;

class ItemController extends Controller
{
    /**
     * Mostrar listado de items.
     */
    public function index()
    {
        // Obtener items paginados (10 por página)
        $items = Item::orderBy('id', 'desc')->paginate(10);
        return view('items.index', compact('items'));
    }

    /**
     * Mostrar formulario para crear un nuevo item.
     */
    public function create()
    {
        return view('items.create');
    }

    /**
     * Guardar un item nuevo en la base de datos.
     */
    public function store(Request $request)
    {
        // Validar entrada
        $request->validate([
            'nombre'   => 'required|string|max:255',
            'valor'    => 'required|numeric|min:0',
        ]);

        // Crear el registro
        Item::create([
            'nombre'   => $request->nombre,
            'valor'    => $request->valor,
        ]);

        return redirect()
            ->route('items.index')
            ->with('success', 'Item creado correctamente.');
    }

    /**
     * Mostrar un item en particular.
     */
    public function show(Item $item)
    {
        return view('items.show', compact('item'));
    }

    /**
     * Mostrar formulario para editar un item existente.
     */
    public function edit(Item $item)
    {
        return view('items.edit', compact('item'));
    }

    /**
     * Actualizar un item existente.
     */
    public function update(Request $request, Item $item)
    {
        // Validar entrada
        $request->validate([
            'nombre'   => 'required|string|max:255',
            'cantidad' => 'required|integer|min:0',
            'valor'    => 'required|numeric|min:0',
            'tipo'     => 'required|string|max:100',
            'area'     => 'required|string|max:100',
        ]);

        // Actualizar el registro
        $item->update([
            'nombre'   => $request->nombre,
            'cantidad' => $request->cantidad,
            'valor'    => $request->valor,
            'tipo'     => $request->tipo,
            'area'     => $request->area,
        ]);

        return redirect()
            ->route('items.index')
            ->with('success', 'Item actualizado correctamente.');
    }

    /**
     * Eliminar un item.
     */
    public function destroy(Item $item)
    {
        $item->delete();

        return redirect()
            ->route('items.index')
            ->with('success', 'Item eliminado correctamente.');
    }
}

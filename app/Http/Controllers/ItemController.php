<?php

namespace App\Http\Controllers;

use App\Models\Item;
use App\Models\InventarioHistorial;
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
            'tipo'     => 'required|in:0,1',
            'costo'    => 'required_if:tipo,1|nullable|numeric|min:0',
            'cantidad' => 'required_if:tipo,1|nullable|integer|min:0',
            'area'     => 'nullable|integer',
        ]);

        // Crear el registro
        $item = Item::create([
            'nombre'   => $request->nombre,
            'valor'    => $request->valor,
            'tipo'     => $request->tipo,
            'costo'    => $request->costo,
            'cantidad' => $request->tipo == 1 ? $request->cantidad : null,
            'area'     => $request->area,
        ]);

        if ($request->tipo == 1 && $request->cantidad) {
            InventarioHistorial::create([
                'item_id'    => $item->id,
                'cambio'     => $request->cantidad,
                'descripcion'=> 'Carga inicial',
            ]);
        }

        return redirect()
            ->route('items.index')
            ->with('success', 'Item creado correctamente.');
    }

    /**
     * Mostrar un item en particular.
     */
    public function show(Item $item)
    {
        $item->load('movimientos');
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
            'valor'    => 'required|numeric|min:0',
            'tipo'     => 'required|in:0,1',
            'costo'    => 'required_if:tipo,1|nullable|numeric|min:0',
            'cantidad' => 'required_if:tipo,1|nullable|integer|min:0',
            'area'     => 'nullable|integer',
        ]);

        $item->update([
            'nombre'   => $request->nombre,
            'valor'    => $request->valor,
            'tipo'     => $request->tipo,
            'costo'    => $request->costo,
            'cantidad' => $request->tipo == 1 ? $request->cantidad : null,
            'area'     => $request->area,
        ]);

        return redirect()
            ->route('items.index')
            ->with('success', 'Item actualizado correctamente.');
    }

    public function addUnitsForm(Item $item)
    {
        return view('items.add-stock', compact('item'));
    }

    public function addUnits(Request $request, Item $item)
    {
        $data = $request->validate([
            'cantidad' => 'required|integer|min:1',
        ]);

        $item->increment('cantidad', $data['cantidad']);

        InventarioHistorial::create([
            'item_id'    => $item->id,
            'cambio'     => $data['cantidad'],
            'descripcion'=> 'Ingreso manual',
        ]);

        return redirect()
            ->route('items.show', $item)
            ->with('success', 'Stock actualizado correctamente.');
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

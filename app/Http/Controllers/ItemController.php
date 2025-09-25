<?php

namespace App\Http\Controllers;

use App\Models\Item;
use App\Models\InventarioHistorial;
use App\Models\Area;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

class ItemController extends Controller
{
    /**
     * Mostrar listado de items.
     */
    public function index(Request $request)
    {
        $query = Item::with('areaRelation');

        if ($request->filled('search')) {
            $query->where('nombre', 'like', '%' . $request->search . '%');
        }

        $items = $query->orderBy('id', 'desc')
                       ->paginate(10)
                       ->appends($request->all());

        return view('items.index', compact('items'));
    }

    /**
     * Mostrar formulario para crear un nuevo item.
     */
    public function create()
    {
        $areas = Area::orderBy('descripcion')->get();

        return view('items.create', compact('areas'));
    }

    /**
     * Guardar un item nuevo en la base de datos.
     */
    public function store(Request $request)
    {
        $request->merge([
            'valor' => $this->normalizeCurrency($request->input('valor')),
            'costo' => $this->normalizeCurrency($request->input('costo')),
        ]);

        // Validar entrada
        $request->validate([
            'nombre'   => 'required|string|max:255',
            'valor'    => 'required|numeric|min:0',
            'tipo'     => 'required|in:0,1',
            'costo'    => 'required_if:tipo,1|nullable|numeric|min:0',
            'cantidad' => 'required_if:tipo,1|nullable|integer|min:0',
            'area'     => ['nullable', Rule::exists('tenant.areas', 'id')],
        ]);

        // Crear el registro
        $item = Item::create([
            'nombre'   => $request->nombre,
            'valor'    => $request->valor,
            'tipo'     => $request->tipo,
            'costo'    => $request->costo,
            'cantidad' => $request->tipo == 1 ? $request->cantidad : null,
            'area'     => $request->area ?: null,
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
        $item->load('movimientos', 'areaRelation');
        return view('items.show', compact('item'));
    }

    /**
     * Mostrar formulario para editar un item existente.
     */
    public function edit(Item $item)
    {
        $areas = Area::orderBy('descripcion')->get();

        return view('items.edit', compact('item', 'areas'));
    }

    /**
     * Actualizar un item existente.
     */
    public function update(Request $request, Item $item)
    {
        $request->merge([
            'valor' => $this->normalizeCurrency($request->input('valor')),
            'costo' => $this->normalizeCurrency($request->input('costo')),
        ]);

        // Validar entrada
        $request->validate([
            'nombre'   => 'required|string|max:255',
            'valor'    => 'required|numeric|min:0',
            'tipo'     => 'required|in:0,1',
            'costo'    => 'required_if:tipo,1|nullable|numeric|min:0',
            'cantidad' => 'required_if:tipo,1|nullable|integer|min:0',
            'area'     => ['nullable', Rule::exists('tenant.areas', 'id')],
        ]);

        $item->update([
            'nombre'   => $request->nombre,
            'valor'    => $request->valor,
            'tipo'     => $request->tipo,
            'costo'    => $request->costo,
            'cantidad' => $request->tipo == 1 ? $request->cantidad : null,
            'area'     => $request->area ?: null,
        ]);

        return redirect()
            ->route('items.index')
            ->with('success', 'Item actualizado correctamente.');
    }

    private function normalizeCurrency($value): ?float
    {
        if ($value === null || $value === '') {
            return null;
        }

        if (is_numeric($value)) {
            return (float) $value;
        }

        $normalized = preg_replace('/[^\d,.-]/', '', (string) $value);
        $normalized = str_replace('.', '', $normalized);
        $normalized = str_replace(',', '.', $normalized);

        return is_numeric($normalized) ? (float) $normalized : null;
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

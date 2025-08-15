<?php

namespace App\Http\Controllers;
use Illuminate\Support\Facades\DB;   
use App\Models\Membresia;
use App\Models\Item;
use App\Models\Alumno;
use Illuminate\Http\Request;

class MembresiaController extends Controller
{
    /**
     * Mostrar listado de membresías.
     */
    public function index()
    {
        $membresias = Membresia::orderBy('id', 'asc')->get();
        return view('membresias.index', compact('membresias'));
    }

    /**
     * Guardar nueva membresía.
     */
   public function store(Request $request)
{
    $data = $request->validate([
        'descripcion' => 'required|string|max:255',
        'clases'      => 'required|integer|min:0',
        'reservas'    => 'required|integer|min:0',
        'valor'       => 'required|numeric|min:0',
    ]);
	
	
 DB::transaction(function() use ($data) {
            // 1) Crear el ítem primero
            $item = Item::create([
                'nombre' => $data['descripcion'],
                'valor'  => $data['valor'],
                // ...otros campos obligatorios de Item
            ]);

            // 2) Crear la membresía y guardar el item_id
            Membresia::create([
                'descripcion' => $data['descripcion'],
                'clases'      => $data['clases'],
                'reservas'    => $data['reservas'],
                'valor'       => $data['valor'],
                'item'     => $item->id,   // <-- aquí enlazamos la membresía con el ítem
            ]);
        });

    return redirect()
        ->route('membresias.index')
        ->with('success', 'Membresía e ítem creados correctamente.');
}


    /**
     * Actualizar membresía.
     */
    public function update(Request $request, Membresia $membresia)
    {
        $request->validate([
            'descripcion' => 'required|string|max:255',
            'clases'      => 'required|integer|min:0',
            'reservas'    => 'required|integer|min:0',
            'valor'       => 'required|numeric|min:0',
        ]);

        $membresia->update([
            'descripcion' => $request->descripcion,
            'clases'      => $request->clases,
            'reservas'    => $request->reservas,
            'valor'       => $request->valor,
        ]);

        return redirect()->route('membresias.index')
                         ->with('success', 'Membresía actualizada correctamente.');
    }

    /**
     * Eliminar membresía.
     */
    public function destroy(Membresia $membresia)
    {
        $membresia->delete();
        return redirect()->route('membresias.index')
                         ->with('success', 'Membresía eliminada correctamente.');
    }
	
	 public function show(Request $request)
    {
        $jugadores   = Alumno::all();
        $membresias  = Membresia::all();
        // Si ya generaste/almacenaste la orden de compra antes, podrías recuperarla:
        $ordenCompra = session('orden_compra_id') ?? null;

        return view('membresias.make', compact('jugadores', 'membresias', 'ordenCompra'));
    }
}

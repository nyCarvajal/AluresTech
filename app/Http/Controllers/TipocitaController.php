<?php

namespace App\Http\Controllers;

use App\Models\Tipocita;
use Illuminate\Http\Request;

class TipocitaController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $tipocitas = Tipocita::orderByDesc('id')->paginate(10);

        return view('tipocitas.index', compact('tipocitas'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        return view('tipocitas.create');
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $data = $request->validate([
            'nombre' => ['required', 'string', 'max:255'],
        ]);

        Tipocita::create($data);

        return redirect()
            ->route('tipocitas.index')
            ->with('success', 'Tipo de cita creado correctamente.');
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Tipocita $tipocita)
    {
        return view('tipocitas.edit', compact('tipocita'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Tipocita $tipocita)
    {
        $data = $request->validate([
            'nombre' => ['required', 'string', 'max:255'],
        ]);

        $tipocita->update($data);

        return redirect()
            ->route('tipocitas.index')
            ->with('success', 'Tipo de cita actualizada correctamente.');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Tipocita $tipocita)
    {
        $tipocita->delete();

        return redirect()
            ->route('tipocitas.index')
            ->with('success', 'Tipo de cita eliminada correctamente.');
    }
}

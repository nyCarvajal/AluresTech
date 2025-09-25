<?php

namespace App\Http\Controllers;

use App\Models\TipoIdentificacion;
use Illuminate\Http\Request;

class TipoIdentificacionController extends Controller
{
    public function index()
    {
        $tiposIdentificacion = TipoIdentificacion::orderBy('id')->paginate(10);

        return view('tipo-identificaciones.index', compact('tiposIdentificacion'));
    }

    public function create()
    {
        return view('tipo-identificaciones.create');
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'tipo' => 'required|string|max:100',
        ]);

        TipoIdentificacion::create($data);

        return redirect()
            ->route('tipo-identificaciones.index')
            ->with('success', 'Tipo de identificación creado correctamente.');
    }

    public function edit(TipoIdentificacion $tipoIdentificacion)
    {
        return view('tipo-identificaciones.edit', compact('tipoIdentificacion'));
    }

    public function update(Request $request, TipoIdentificacion $tipoIdentificacion)
    {
        $data = $request->validate([
            'tipo' => 'required|string|max:100',
        ]);

        $tipoIdentificacion->update($data);

        return redirect()
            ->route('tipo-identificaciones.index')
            ->with('success', 'Tipo de identificación actualizado correctamente.');
    }

    public function destroy(TipoIdentificacion $tipoIdentificacion)
    {
        $tipoIdentificacion->delete();

        return redirect()
            ->route('tipo-identificaciones.index')
            ->with('success', 'Tipo de identificación eliminado correctamente.');
    }
}

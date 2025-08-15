<?php

namespace App\Http\Controllers;

use App\Models\Cancha;
use App\Models\Deporte;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class DeporteController extends Controller
{
    /**
     * Display a listing of the resource.
     */
	 public function index()
{
    $deportes = Deporte::all();
    return view('deportes.index', compact('deportes'));
}

public function create()
{
	
	
    return view('deportes.create');

}

public function store(Request $request)
{
    $data = $request->validate([
      'deporte'      => 'required|string|max:255',
     
    ]);

    Deporte::create($data);

    return redirect()->route('deportes.index')
                     ->with('success','Deporte creado correctamente');
}

public function show(Deporte $deporte)
{
    return view('deportes.show', compact('deporte'));
}

public function edit(Deporte $deporte)
{
    return view('deportes.edit', compact( 'deporte'));
}

   public function update(Request $request, Deporte $deporte)
    {
        $data = $request->validate([
            // permite el mismo nombre si no lo cambias
            'deporte' => 'required|string|max:255|unique:deportes,deporte,' . $deporte->id,
        ]);

        $deporte->update($data);

        return redirect()
            ->route('deportes.index')
            ->with('success', 'Deporte actualizado correctamente.');
    }

public function destroy(Deporte $deporte)
{
    $deporte->delete();
    return redirect()->route('deportes.index')
                     ->with('success','Deporte eliminado correctamente');
}

}
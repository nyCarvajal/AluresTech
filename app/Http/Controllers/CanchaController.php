<?php

namespace App\Http\Controllers;

use App\Models\Cancha;
use App\Models\Deporte;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class CanchaController extends Controller
{
    /**
     * Display a listing of the resource.
     */
	 public function index()
{
    $canchas = Cancha::all();
    return view('canchas.index', compact('canchas'));
}

public function create()
{
    $deportes = Deporte::orderBy('deporte')->get();
    return view('canchas.create', compact('deportes'));

}

public function store(Request $request)
{
    $data = $request->validate([
      'nombre'      => 'required|string|max:255',
      'capacidad'   => 'required|integer|min:0',
      'valor' => 'required|numeric|min:0',
	  'deporte_id'=>'required|string',
    ]);

    Cancha::create($data);

    return redirect()->route('canchas.index')
                     ->with('success','Cancha creada correctamente');
}

public function show($id)
{
	$cancha=Cancha::firstWhere('id', $id);
    return view('canchas.show', compact('cancha'));
}

public function edit($id)
{
	$cancha=Cancha::firstWhere('id', $id);
	$deportes = Deporte::orderBy('deporte')->get();
    return view('canchas.edit', compact('deportes', 'cancha'));
}

public function update(Request $request, Cancha $cancha)
{
    $data = $request->validate([
      'nombre'      => 'required|string|max:255',
      'deporte_id'=>'required|string',
      'capacidad'   => 'required|integer|min:0',
      'valor' => 'required|numeric|min:0',
    ]);

    $cancha->update($data);

    return redirect()->route('canchas.index')
                     ->with('success','Cancha actualizada correctamente');
}

public function destroy(Cancha $cancha)
{
    $cancha->delete();
    return redirect()->route('canchas.index')
                     ->with('success','Cancha eliminada correctamente');
}

}
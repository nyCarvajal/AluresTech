<?php

namespace App\Http\Controllers;

use App\Models\Area;
use Illuminate\Http\Request;

class AreaController extends Controller
{
    public function index()
    {
        $areas = Area::orderBy('descripcion')->paginate(10);

        return view('areas.index', compact('areas'));
    }

    public function create()
    {
        return view('areas.create');
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'descripcion' => 'required|string|max:300',
        ]);

        Area::create($data);

        return redirect()
            ->route('areas.index')
            ->with('success', 'Área creada correctamente.');
    }

    public function edit(Area $area)
    {
        return view('areas.edit', compact('area'));
    }

    public function update(Request $request, Area $area)
    {
        $data = $request->validate([
            'descripcion' => 'required|string|max:300',
        ]);

        $area->update($data);

        return redirect()
            ->route('areas.index')
            ->with('success', 'Área actualizada correctamente.');
    }
}

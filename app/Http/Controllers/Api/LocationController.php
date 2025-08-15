<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Departamentos;
use App\Models\Municipios;
use Illuminate\Http\Request;

class LocationController extends Controller
{
    // GET /api/departamentos?pais_id=#
    public function departamentos(Request $request)
    {
        $request->validate(['pais_id' => 'required|exists:paises,id']);
        $list = Departamentos::where('pais_id', $request->pais_id)
                            ->orderBy('nombre')
                            ->get(['id','nombre']);
        return response()->json($list);
    }

    // GET /api/municipios?departamento_id=#
    public function municipios(Request $request)
    {
        $request->validate(['departamento_id' => 'required|exists:departamentos,id']);
        $list = Municipios::where('departamento_id', $request->departamento_id)
                         ->orderBy('nombre')
                         ->get(['id','nombre']);
        return response()->json($list);
    }
}

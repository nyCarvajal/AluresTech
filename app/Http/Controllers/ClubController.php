<?php

namespace App\Http\Controllers;

use App\Models\Club;
use Illuminate\Http\Request;

class ClubController extends Controller
{
   
   // app/Http/Controllers/ClubController.php

public function editOwn()
{
    // Asumo que tu modelo User tiene relación club()
   $club=auth()->user()->club;
    return view('clubes.edit', compact('club'));
}

public function updateOwn(Request $request)
{
    $club = auth()->user()->club;

    $data = $request->validate([
        'nombre'           => 'required|string',
        'msj_finalizado'         => 'nullable|string',
        'terminos'         => 'nullable|string',
        'color'            => 'nullable|string',
        'msj_reserva_confirmada' => 'nullable|string',
        'msj_bienvenida'   => 'nullable|string',
        'nit'              => 'nullable|string',
        'direccion'        => 'nullable|string',
        'municipio'        => 'nullable|string',
    ]);

    // Normaliza checkboxes
    $data['pos']         = $request->has('pos');
    $data['cuentaCobro'] = $request->has('cuentaCobro');
    $data['electronica'] = $request->has('electronica');

    $club->update($data);

    return redirect()
        ->route('clubes.show', $club->id)
        ->with('success', 'Datos de tu club actualizados.');
}

 public function update(Request $request, Club $club)
    {
        $data = $request->validate([
            'nombre'           => 'required|string',
           
            'cuentaCobro'      => 'nullable|boolean',
            'msj_finalizado'   => 'nullable|string',
            'terminos'         => 'nullable|string',
            'color'            => 'nullable|string',
            'msj_recordatorio' => 'nullable|string',
            'msj_bienvenida'   => 'nullable|string',
            'nit'              => 'nullable|string',
            'direccion'        => 'nullable|string',
            'municipio'        => 'nullable|string',
        ]);

        $data['pos'] = $request->has('pos');
        $data['cuentaCobro'] = $request->has('cuentaCobro');
        $data['electronica'] = $request->has('electronica');

        $club->update($data);
        return redirect()->route('clubes.show', compact('club'));
    }
	
	public function showOwn(){
		
		return view('clubes.show');
	}
	
	public function show(){
		 $club=auth()->user()->club;
		return view('clubes.show', compact('club'));
	}

 

    public function destroy(Club $club)
    {
        $club->delete();
        return back();
    }
}


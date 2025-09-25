<?php

namespace App\Http\Controllers;

use App\Models\Peluqueria;
use Illuminate\Http\Request;

class PeluqueriaController extends Controller
{
   
   // app/Http/Controllers/PeluqueriaController.php

public function editOwn()
{
    // Asumo que tu modelo User tiene relación peluqueria()
   $peluqueria=auth()->user()->peluqueria;
    return view('peluquerias.edit', compact('peluqueria'));
}

public function updateOwn(Request $request)
{
    $peluqueria = auth()->user()->peluqueria;

    $data = $request->validate([
        'nombre'           => 'required|string', 
      
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

    $peluqueria->update($data);

    return redirect()
        ->route('peluquerias.show', $peluqueria->id)
        ->with('success', 'Datos de tu peluqueria actualizados.');
}

 public function update(Request $request, Peluqueria $peluqueria)
    {
        $data = $request->validate([
            'nombre'           => 'required|string',
           
            'cuentaCobro'      => 'nullable|boolean',
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

        $peluqueria->update($data);
        return redirect()->route('peluquerias.show', compact('peluqueria'));
    }
	
	public function showOwn(){
		
		return view('peluquerias.show');
	}
	
	public function show(){
		 $peluqueria=auth()->user()->peluqueria;
		return view('peluquerias.show', compact('peluqueria'));
	}

 

    public function destroy(Peluqueria $peluqueria)
    {
        $peluqueria->delete();
        return back();
    }
}


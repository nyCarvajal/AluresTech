<?php

namespace App\Http\Controllers;

use App\Models\Peluqueria;
use CloudinaryLabs\CloudinaryLaravel\Facades\Cloudinary;
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
        'menu_color'       => ['nullable', 'regex:/^#[0-9A-Fa-f]{6}$/'],
        'topbar_color'     => ['nullable', 'regex:/^#[0-9A-Fa-f]{6}$/'],
        'msj_reserva_confirmada' => 'nullable|string',
        'msj_bienvenida'   => 'nullable|string',
        'nit'              => 'nullable|string',
        'direccion'        => 'nullable|string',
        'municipio'        => 'nullable|string',
        'logo'             => 'nullable|image|mimes:jpeg,png,jpg,gif,webp|max:10240',
    ]);

    // Normaliza checkboxes
    $data['pos']         = $request->has('pos');
    $data['cuentaCobro'] = $request->has('cuentaCobro');
    $data['electronica'] = $request->has('electronica');

    $data['menu_color']   = $data['menu_color'] ?? null;
    $data['topbar_color'] = $data['topbar_color'] ?? null;

    if ($request->hasFile('logo')) {
        $uploadResult = Cloudinary::uploadApi()
            ->upload(
                $request->file('logo')->getRealPath(),
                ['folder' => 'peluquerias']
            );

        $data['logo'] = $uploadResult['public_id'] ?? null;
    } else {
        unset($data['logo']);
    }

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
            'menu_color'       => ['nullable', 'regex:/^#[0-9A-Fa-f]{6}$/'],
            'topbar_color'     => ['nullable', 'regex:/^#[0-9A-Fa-f]{6}$/'],
            'msj_recordatorio' => 'nullable|string',
            'msj_bienvenida'   => 'nullable|string',
            'nit'              => 'nullable|string',
            'direccion'        => 'nullable|string',
            'municipio'        => 'nullable|string',
            'logo'             => 'nullable|image|mimes:jpeg,png,jpg,gif,webp|max:10240',
        ]);

        $data['pos'] = $request->has('pos');
        $data['cuentaCobro'] = $request->has('cuentaCobro');
        $data['electronica'] = $request->has('electronica');

        $data['menu_color']   = $data['menu_color'] ?? null;
        $data['topbar_color'] = $data['topbar_color'] ?? null;

        if ($request->hasFile('logo')) {
            $uploadResult = Cloudinary::uploadApi()
                ->upload(
                    $request->file('logo')->getRealPath(),
                    ['folder' => 'peluquerias']
                );

            $data['logo'] = $uploadResult['public_id'] ?? null;
        } else {
            unset($data['logo']);
        }

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


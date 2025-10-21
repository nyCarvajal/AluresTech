<?php

namespace App\Http\Controllers;

use App\Models\Peluqueria;
use CloudinaryLabs\CloudinaryLaravel\Facades\Cloudinary;
use Illuminate\Http\Request;
use Illuminate\Http\UploadedFile;
use Illuminate\Validation\ValidationException;

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

    $peluqueria->update($this->prepareUpdateData($request, $data));

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

        $peluqueria->update($this->prepareUpdateData($request, $data));
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

    protected function prepareUpdateData(Request $request, array $data): array
    {
        $data['pos'] = $request->has('pos');
        $data['cuentaCobro'] = $request->has('cuentaCobro');
        $data['electronica'] = $request->has('electronica');

        $data['menu_color'] = $data['menu_color'] ?? null;
        $data['topbar_color'] = $data['topbar_color'] ?? null;

        if ($request->hasFile('logo')) {
            $data['logo'] = $this->uploadLogo($request->file('logo'));
        } else {
            unset($data['logo']);
        }

        return $data;
    }

    protected function uploadLogo(UploadedFile $file): string
    {
        if (! $this->cloudinaryIsConfigured()) {
            throw ValidationException::withMessages([
                'logo' => 'No se puede subir el logo porque Cloudinary no está configurado correctamente.',
            ]);
        }

        $folder = trim(config('cloudinary.upload.folder') ?? '', '/');
        if ($folder === '') {
            $folder = 'peluquerias';
        }

        try {
            $uploadedFile = Cloudinary::uploadFile(
                $file->getRealPath(),
                [
                    'folder' => $folder,
                    'resource_type' => 'image',
                ]
            );

            return $uploadedFile->getPublicId();
        } catch (\Throwable $exception) {
            report($exception);

            throw ValidationException::withMessages([
                'logo' => 'Ocurrió un error al subir el logo. Por favor inténtalo de nuevo más tarde.',
            ]);
        }
    }

    protected function cloudinaryIsConfigured(): bool
    {
        $config = config('cloudinary.cloud', []);

        return ! empty($config['cloud_name'])
            && ! empty($config['api_key'])
            && ! empty($config['api_secret']);
    }
}


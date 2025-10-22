<?php

namespace App\Http\Controllers;

use App\Models\Peluqueria;
use CloudinaryLabs\CloudinaryLaravel\Facades\Cloudinary;
use Illuminate\Http\Request;
use Illuminate\Http\UploadedFile;
use Illuminate\Validation\ValidationException;

class PeluqueriaController extends Controller
{
    public function editOwn()
    {
        $peluqueria = auth()->user()->peluqueria;
        $formAction = route('peluquerias.update');

        return view('peluquerias.edit', [
            'peluqueria' => $peluqueria,
            'formAction' => $formAction,
            'stylistLabelSingular' => $peluqueria?->trainer_label_singular,
            'stylistLabelPlural' => $peluqueria?->trainer_label_plural,
        ]);
    }

    public function updateOwn(Request $request)
    {
        $peluqueria = auth()->user()->peluqueria;

        $data = $request->validate([
            'nombre'                  => 'required|string',
            'color'                   => 'nullable|string',
            'menu_color'              => ['nullable', 'regex:/^#[0-9A-Fa-f]{6}$/'],
            'topbar_color'            => ['nullable', 'regex:/^#[0-9A-Fa-f]{6}$/'],
            'msj_reserva_confirmada'  => 'nullable|string',
            'msj_bienvenida'          => 'nullable|string',
            'trainer_label_singular'  => 'nullable|string|max:191',
            'trainer_label_plural'    => 'nullable|string|max:191',
            'nit'                     => 'nullable|string',
            'direccion'               => 'nullable|string',
            'municipio'               => 'nullable|string',
            'logo'                    => 'nullable|image|mimes:jpeg,png,jpg,gif,webp|max:10240',
        ]);

        $updateData = $this->prepareUpdateData($request, $data);

        $peluqueria->update($updateData);

        $this->syncStylistLabel(
            $peluqueria,
            $updateData['trainer_label_singular'] ?? null,
            $updateData['trainer_label_plural'] ?? null
        );

        return redirect()
            ->route('peluquerias.perfil')
            ->with('success', 'Datos de tu peluquería actualizados.');
    }

    public function showOwn()
    {
        $peluqueria = auth()->user()->peluqueria;

        return view('peluquerias.show', compact('peluqueria'));
    }

    public function show()
    {
        $peluqueria = auth()->user()->peluqueria;
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

        $data['trainer_label_singular'] = $this->sanitizeRoleLabel($request->input('trainer_label_singular'));
        $data['trainer_label_plural'] = $this->sanitizeRoleLabel($request->input('trainer_label_plural'));

        if ($request->hasFile('logo')) {
            $data['logo'] = $this->uploadLogo($request->file('logo'));
        } else {
            unset($data['logo']);
        }

        return $data;
    }

    protected function sanitizeRoleLabel(?string $label): ?string
    {
        $label = trim((string) ($label ?? ''));

        return $label === '' ? null : $label;
    }

    protected function uploadLogo(UploadedFile $file): string
    {
        if (!$this->cloudinaryIsConfigured()) {
            throw ValidationException::withMessages([
                'logo' => 'No se puede subir el logo porque Cloudinary no está configurado correctamente. Verifica tus credenciales (CLOUDINARY_URL, CLOUDINARY_CLOUD_NAME, CLOUDINARY_API_KEY, CLOUDINARY_API_SECRET) y evita usar los valores de ejemplo "demo".',
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
        $primaryConfig = config('cloudinary.cloud', []);
        if ($this->cloudinaryCredentialsAreUsable(
            $primaryConfig['cloud_name'] ?? null,
            $primaryConfig['api_key'] ?? null,
            $primaryConfig['api_secret'] ?? null,
        )) {
            return true;
        }

        $filesystemConfig = config('filesystems.disks.cloudinary', []);
        if ($this->cloudinaryCredentialsAreUsable(
            $filesystemConfig['cloud'] ?? null,
            $filesystemConfig['key'] ?? null,
            $filesystemConfig['secret'] ?? null,
        )) {
            return true;
        }

        if ($this->cloudinaryUrlContainsCredentials(config('cloudinary.url'))) {
            return true;
        }

        if ($this->cloudinaryUrlContainsCredentials($filesystemConfig['url'] ?? null)) {
            return true;
        }

        if ($this->cloudinaryUrlContainsCredentials(env('CLOUDINARY_URL'))) {
            return true;
        }

        return false;
    }

    private function cloudinaryCredentialsAreUsable(?string $cloudName, ?string $apiKey, ?string $apiSecret): bool
    {
        $credentials = [$cloudName, $apiKey, $apiSecret];
        $placeholders = ['demo', 'cloud_name', 'api_key', 'api_secret', 'your_cloud_name', 'your_api_key', 'your_api_secret'];

        foreach ($credentials as $credential) {
            $credential = is_string($credential) ? trim($credential) : '';

            if ($credential === '') {
                return false;
            }

            if (in_array(strtolower($credential), $placeholders, true)) {
                return false;
            }
        }

        return true;
    }

    private function cloudinaryUrlContainsCredentials(string|array|null $url): bool
    {
        if (!$url) {
            return false;
        }

        if (is_array($url)) {
            return false;
        }

        $parts = parse_url($url);

        if ($parts === false) {
            return false;
        }

        $cloudName = $parts['host'] ?? null;
        $apiKey = $parts['user'] ?? null;
        $apiSecret = $parts['pass'] ?? null;

        return $this->cloudinaryCredentialsAreUsable($cloudName, $apiKey, $apiSecret);
    }

    protected function syncStylistLabel(Peluqueria $peluqueria, ?string $singular, ?string $plural): void
    {
        $singular = trim((string) ($singular ?? ''));
        $plural = trim((string) ($plural ?? ''));

        if ($singular === '' && $plural === '') {
            $peluqueria->roleLabels()
                ->where('role', Peluqueria::ROLE_STYLIST)
                ->delete();

            return;
        }

        $peluqueria->roleLabels()->updateOrCreate(
            ['role' => Peluqueria::ROLE_STYLIST],
            [
                'singular' => $singular === ''
                    ? Peluqueria::defaultRoleLabel(Peluqueria::ROLE_STYLIST)
                    : $singular,
                'plural' => $plural === ''
                    ? Peluqueria::defaultRoleLabel(Peluqueria::ROLE_STYLIST, true)
                    : $plural,
            ]
        );
    }
}

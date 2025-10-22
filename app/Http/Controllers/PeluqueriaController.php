<?php

namespace App\Http\Controllers;

use App\Models\Peluqueria;
use CloudinaryLabs\CloudinaryLaravel\Facades\Cloudinary;
use Illuminate\Http\Request;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\Storage;
use Illuminate\Validation\ValidationException;

class PeluqueriaController extends Controller
{
    private ?bool $hasLogoUrlColumn = null;

    public function editOwn()
    {
        $peluqueria = auth()->user()->peluqueria;
        $formAction = route('peluquerias.update');

        $stylistLabels = RoleLabelResolver::forStylist($peluqueria);

        return view('peluquerias.edit', [
            'peluqueria' => $peluqueria,
            'formAction' => $formAction,
            'stylistLabelSingular' => $stylistLabels['singular'],
            'stylistLabelPlural' => $stylistLabels['plural'],
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
            'trainer_label_singular'  => 'nullable|string|max:191',
            'trainer_label_plural'    => 'nullable|string|max:191',
            'logo'                    => ['nullable', 'file', 'mimes:jpeg,png,jpg,gif,webp', 'max:10240'],
        ]);

        $updateData = $this->prepareUpdateData($request, $data);

        $peluqueria->update($updateData);

        $this->syncStylistLabel(
            $peluqueria,
            $updateData['trainer_label_singular'] ?? null,
            $updateData['trainer_label_plural'] ?? null
        );

        $this->syncStylistLabel(
            $peluqueria,
            $data['trainer_label_singular'] ?? null,
            $data['trainer_label_plural'] ?? null
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

        $hasLogoUrlColumn = $this->peluqueriasHasLogoUrlColumn();

        if ($request->hasFile('logo')) {
            $upload = $this->uploadLogo($request->file('logo'));

            $data['logo'] = $upload['logo'];

            $logoUrl = $upload['logo_url'] ?? null;

            if ($hasLogoUrlColumn && $logoUrl) {
                $data['logo_url'] = $logoUrl;
            } elseif ($logoUrl && filter_var($logoUrl, FILTER_VALIDATE_URL)) {
                $data['logo'] = $logoUrl;
            }
        } else {
            unset($data['logo']);

            if ($hasLogoUrlColumn) {
                unset($data['logo_url']);
            }
        }

        unset($data['trainer_label_singular'], $data['trainer_label_plural']);

        return $data;
    }

    public function sanitizeRoleLabel(Request $request)
    {
        $validated = $request->validate([
            'value' => 'nullable|string|max:191',
            'role' => 'nullable',
            'form' => 'nullable|in:singular,plural',
        ]);

        $role = $this->determineRoleIdentifier($validated['role'] ?? null);
        $isPlural = ($validated['form'] ?? 'singular') === 'plural';

        $normalized = $this->normalizeRoleLabelInput($validated['value'] ?? null, $role, $isPlural);
        $default = Peluqueria::defaultRoleLabel($role, $isPlural);

        return response()->json([
            'value' => $normalized ?? $default,
            'default' => $default,
            'is_default' => $normalized === null,
        ]);
    }

    protected function uploadLogo(UploadedFile $file): array
    {
        if ($this->cloudinaryIsConfigured()) {
            $folder = $this->cloudinaryUploadFolder();

            try {
                $uploadedFile = Cloudinary::uploadFile(
                    $file->getRealPath(),
                    [
                        'folder' => $folder,
                        'resource_type' => 'image',
                    ]
                );

                $secureUrl = null;
                if (method_exists($uploadedFile, 'getSecurePath')) {
                    $secureUrl = $uploadedFile->getSecurePath();
                } elseif (method_exists($uploadedFile, 'getSecureUrl')) {
                    $secureUrl = $uploadedFile->getSecureUrl();
                }

                $publicId = method_exists($uploadedFile, 'getPublicId')
                    ? $uploadedFile->getPublicId()
                    : null;

                $resultUrl = null;

                if (method_exists($uploadedFile, 'getResult')) {
                    $result = $uploadedFile->getResult();

                    if (is_array($result)) {
                        if (! $secureUrl && ! empty($result['secure_url'])) {
                            $secureUrl = $result['secure_url'];
                        }

                        if (! $publicId && ! empty($result['public_id'])) {
                            $publicId = $result['public_id'];
                        }

                        if (! empty($result['url'])) {
                            $resultUrl = $result['url'];
                        }
                    }
                }

                if (! $secureUrl && $resultUrl && filter_var($resultUrl, FILTER_VALIDATE_URL)) {
                    $secureUrl = $resultUrl;
                }

                if (! $publicId) {
                    if ($secureUrl) {
                        $publicId = $secureUrl;
                    } elseif ($resultUrl) {
                        $publicId = $resultUrl;
                    }
                }

                if (! $publicId) {
                    throw new \RuntimeException('No se recibió un identificador público de Cloudinary.');
                }

                return [
                    'logo' => $publicId,
                    'logo_url' => $secureUrl ?? (filter_var($publicId, FILTER_VALIDATE_URL) ? $publicId : null),
                ];
            } catch (\Throwable $exception) {
                report($exception);

                throw ValidationException::withMessages([
                    'logo' => 'Ocurrió un error al subir el logo a Cloudinary. Verifica la configuración e inténtalo de nuevo.',
                ]);
            }
        }

        return $this->storeLogoOnPublicDisk($file);
    }

    protected function storeLogoOnPublicDisk(UploadedFile $file): array
    {
        try {
            $path = $file->store('peluquerias', 'public');
        } catch (\Throwable $exception) {
            report($exception);

            throw ValidationException::withMessages([
                'logo' => 'Ocurrió un error al subir el logo. Por favor inténtalo de nuevo más tarde.',
            ]);
        }

        if ($path === false) {
            throw ValidationException::withMessages([
                'logo' => 'Ocurrió un error al subir el logo. Por favor inténtalo de nuevo más tarde.',
            ]);
        }

        $this->mirrorPublicStorageFile($path);

        $logoUrl = null;

        try {
            $logoUrl = Storage::disk('public')->url($path);
        } catch (\Throwable $exception) {
            report($exception);
        }

        if (is_string($logoUrl) && $logoUrl !== '' && ! filter_var($logoUrl, FILTER_VALIDATE_URL)) {
            $logoUrl = url($logoUrl);
        }

        $result = [
            'logo' => $path,
        ];

        if (is_string($logoUrl) && $logoUrl !== '') {
            $result['logo_url'] = $logoUrl;
        }

        return $result;
    }

    protected function cloudinaryUploadFolder(): string
    {
        $filesystemConfig = config('filesystems.disks.cloudinary', []);
        $prefix = '';

        if (! empty($filesystemConfig['prefix']) && is_string($filesystemConfig['prefix'])) {
            $prefix = trim($filesystemConfig['prefix'], '/');
        }

        $configuredFolder = config('cloudinary.upload.folder');
        $folder = is_string($configuredFolder) ? trim($configuredFolder, '/') : '';

        if ($folder === '') {
            $folder = 'peluquerias';
        }

        if ($prefix !== '') {
            return $prefix . '/' . $folder;
        }

        return $folder;
    }

    protected function mirrorPublicStorageFile(string $path): void
    {
        if (config('filesystems.disks.public.driver') !== 'local') {
            return;
        }

        $storagePath = public_path('storage');

        if (is_link($storagePath)) {
            return;
        }

        try {
            $sourcePath = Storage::disk('public')->path($path);
        } catch (\Throwable $exception) {
            report($exception);

            return;
        }

        $targetPath = $storagePath . DIRECTORY_SEPARATOR . ltrim($path, DIRECTORY_SEPARATOR);

        try {
            File::ensureDirectoryExists(dirname($targetPath));
            File::copy($sourcePath, $targetPath);
        } catch (\Throwable $exception) {
            report($exception);
        }

        $targetPath = $storagePath . DIRECTORY_SEPARATOR . ltrim($path, DIRECTORY_SEPARATOR);

        try {
            File::ensureDirectoryExists(dirname($targetPath));
            File::copy($sourcePath, $targetPath);
        } catch (\Throwable $exception) {
            report($exception);
        }

        throw ValidationException::withMessages([
            'logo' => 'Ocurrió un error al subir el logo. Por favor inténtalo de nuevo más tarde.',
        ]);
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

    private function peluqueriasHasLogoUrlColumn(): bool
    {
        if ($this->hasLogoUrlColumn !== null) {
            return $this->hasLogoUrlColumn;
        }

        $model = new Peluqueria();
        $connection = $model->getConnectionName();
        $table = $model->getTable();

        try {
            $schema = $connection
                ? Schema::connection($connection)
                : Schema::connection(config('database.default'));

            return $this->hasLogoUrlColumn = $schema->hasColumn($table, 'logo_url');
        } catch (\Throwable $exception) {
            report($exception);

            return $this->hasLogoUrlColumn = false;
        }
    }

    protected function syncStylistLabel(Peluqueria $peluqueria, ?string $singular, ?string $plural): void
    {
        $singular = $this->normalizeRoleLabelInput($singular, Peluqueria::ROLE_STYLIST, false);
        $plural = $this->normalizeRoleLabelInput($plural, Peluqueria::ROLE_STYLIST, true);

        if ($singular === null && $plural === null) {
            $peluqueria->roleLabels()
                ->where('role', Peluqueria::ROLE_STYLIST)
                ->delete();

            return;
        }

        $peluqueria->roleLabels()->updateOrCreate(
            ['role' => Peluqueria::ROLE_STYLIST],
            [
                'singular' => $singular === null
                    ? Peluqueria::defaultRoleLabel(Peluqueria::ROLE_STYLIST)
                    : $singular,
                'plural' => $plural === null
                    ? Peluqueria::defaultRoleLabel(Peluqueria::ROLE_STYLIST, true)
                    : $plural,
            ]
        );
    }

    protected function normalizeRoleLabelInput(?string $value, int $role, bool $plural): ?string
    {
        if ($value === null) {
            return null;
        }

        $value = strip_tags((string) $value);
        $value = preg_replace('/\s+/u', ' ', $value ?? '') ?? '';
        $value = trim($value);

        if ($value === '') {
            return null;
        }

        if (mb_strlen($value) > 191) {
            $value = mb_substr($value, 0, 191);
        }

        $default = Peluqueria::defaultRoleLabel($role, $plural);

        if (mb_strtolower($value) === mb_strtolower($default)) {
            return null;
        }

        return $value;
    }

    protected function determineRoleIdentifier($role): int
    {
        if (is_int($role)) {
            return $role;
        }

        if (is_string($role)) {
            $normalized = strtolower(trim($role));

            if ($normalized === '') {
                return Peluqueria::ROLE_STYLIST;
            }

            if (ctype_digit($normalized)) {
                return (int) $normalized;
            }

            return match ($normalized) {
                'stylist', 'entrenador', 'trainer', 'peluquero', 'peluquera', 'barbero', 'barbera' => Peluqueria::ROLE_STYLIST,
                default => Peluqueria::ROLE_STYLIST,
            };
        }

        return Peluqueria::ROLE_STYLIST;
    }
}

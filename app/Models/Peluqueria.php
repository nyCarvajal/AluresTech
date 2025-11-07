<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class Peluqueria extends Model
{
    public const ROLE_STYLIST = 11;

    protected $connection = 'mysql';

    protected $fillable = [
        'nombre', 'pos', 'cuentaCobro', 'electronica',
        'terminos', 'color', 'menu_color', 'topbar_color', 'msj_recordatorio', 'msj_bienvenida', 'msj_finalizado', 'msj_reserva_confirmada',
        'nit', 'direccion', 'email', 'municipio', 'db', 'slug', 'logo', 'logo_url',
        'trainer_label_singular', 'trainer_label_plural',
    ];

    protected static array $defaultRoleLabels = [
        self::ROLE_STYLIST => [
            'singular' => 'Estilista',
            'plural' => 'Estilistas',
        ],
    ];

    public function getRouteKeyName(): string
    {
        return 'slug';
    }

    protected static function boot()
    {
        parent::boot();

        static::creating(function (Peluqueria $peluqueria) {
            if (empty($peluqueria->slug)) {
                $peluqueria->slug = static::generateUniqueSlug($peluqueria->nombre);
            }
        });

        static::updating(function (Peluqueria $peluqueria) {
            if ($peluqueria->isDirty('nombre') && empty($peluqueria->getOriginal('slug')) && empty($peluqueria->slug)) {
                $peluqueria->slug = static::generateUniqueSlug($peluqueria->nombre);
            }
        });
    }

    public function roleLabels(): HasMany
    {
        return $this->hasMany(RoleLabel::class);
    }

    public static function defaultRoleLabel(int $role, bool $plural = false): string
    {
        $form = $plural ? 'plural' : 'singular';

        return static::$defaultRoleLabels[$role][$form] ?? ($plural ? 'Roles' : 'Rol');
    }

    public function roleLabel(int $role, bool $plural = false): string
    {
        $form = $plural ? 'plural' : 'singular';

        if ($role === self::ROLE_STYLIST) {
            $column = $plural ? 'trainer_label_plural' : 'trainer_label_singular';
            $value = trim((string) ($this->{$column} ?? ''));

            if ($value !== '') {
                return $value;
            }
        }

        $label = $this->relationLoaded('roleLabels')
            ? $this->roleLabels->firstWhere('role', $role)
            : $this->roleLabels()->where('role', $role)->first();

        if ($label) {
            $labelValue = trim((string) $label->{$form});

            if ($labelValue !== '') {
                return $labelValue;
            }
        }

        return static::defaultRoleLabel($role, $plural);
    }

    protected static function generateUniqueSlug(?string $nombre): string
    {
        $baseSlug = Str::slug($nombre ?? 'peluqueria');
        $slug = $baseSlug;
        $counter = 1;

        while (static::where('slug', $slug)->exists()) {
            $slug = $baseSlug . '-' . $counter++;
        }

        return $slug;
    }

    public function resolvedLogoUrl(): string
    {
        $candidates = [
            $this->logo_url ?? null,
            $this->logo ?? null,
        ];

        foreach ($candidates as $candidate) {
            if (! $candidate) {
                continue;
            }

            if (filter_var($candidate, FILTER_VALIDATE_URL)) {
                return $candidate;
            }

            if (Str::startsWith($candidate, ['/'])) {
                return url($candidate);
            }

            try {
                $publicUrl = Storage::disk('public')->url($candidate);

                if (is_string($publicUrl) && $publicUrl !== '') {
                    if (! filter_var($publicUrl, FILTER_VALIDATE_URL)) {
                        $publicUrl = url($publicUrl);
                    }

                    return $publicUrl;
                }
            } catch (\Throwable $exception) {
                // Ignorar y continuar con el siguiente candidato
            }

            if (Str::startsWith($candidate, ['storage/', 'images/'])) {
                return asset($candidate);
            }

            if (function_exists('cloudinary')) {
                try {
                    return cloudinary()->image($candidate)->toUrl();
                } catch (\Throwable $exception) {
                    // Ignorar y continuar con el siguiente candidato
                }
            }

            return asset('storage/' . ltrim($candidate, '/'));
        }

        return asset('images/logoligth.png');
    }
}

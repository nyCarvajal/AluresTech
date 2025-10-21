<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Str;

class Peluqueria extends Model
{
    public const ROLE_STYLIST = 11;

    protected $connection = 'mysql';

    protected $fillable = [
        'nombre', 'pos', 'cuentaCobro', 'electronica',
        'terminos', 'color', 'menu_color', 'topbar_color', 'msj_recordatorio', 'msj_bienvenida', 'msj_finalizado', 'msj_reserva_confirmada',
        'nit', 'direccion', 'municipio', 'db', 'slug'
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

        $label = $this->relationLoaded('roleLabels')
            ? $this->roleLabels->firstWhere('role', $role)
            : $this->roleLabels()->where('role', $role)->first();

        if ($label && ! empty($label->{$form})) {
            return $label->{$form};
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
}

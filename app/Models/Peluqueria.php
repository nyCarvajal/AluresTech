<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;

class Peluqueria extends Model
{
    protected $connection = 'mysql';

    protected $fillable = [
        'nombre', 'pos', 'cuentaCobro', 'electronica',
        'terminos', 'color', 'msj_recordatorio', 'msj_bienvenida', 'msj_finalizado', 'msj_reserva_confirmada',
        'nit', 'direccion', 'municipio', 'db', 'slug'
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

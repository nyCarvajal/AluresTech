<?php

namespace App\Support;

use App\Models\Peluqueria;
use Illuminate\Support\Facades\Auth;

class RoleLabelResolver
{
    /**
     * Obtiene las etiquetas configuradas para el rol de estilista de una peluquería.
     *
     * @return array{singular: string, plural: string}
     */
    public static function forStylist(?Peluqueria $peluqueria = null): array
    {
        $peluqueria = $peluqueria ?: optional(Auth::user())->peluqueria;

        if ($peluqueria) {
            return [
                'singular' => $peluqueria->roleLabel(Peluqueria::ROLE_STYLIST),
                'plural' => $peluqueria->roleLabel(Peluqueria::ROLE_STYLIST, true),
            ];
        }

        return [
            'singular' => Peluqueria::defaultRoleLabel(Peluqueria::ROLE_STYLIST),
            'plural' => Peluqueria::defaultRoleLabel(Peluqueria::ROLE_STYLIST, true),
        ];
    }
}

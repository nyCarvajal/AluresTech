<?php

namespace App\Http\Controllers;

use App\Models\Peluqueria;
use App\Support\RoleLabelResolver;
use Illuminate\Http\Request;

class PeluqueriaController extends Controller
{
    public function editOwn()
    {
        $peluqueria = auth()->user()->peluqueria;

        if ($peluqueria) {
            $peluqueria->load('roleLabels');
        }

        $stylistLabels = RoleLabelResolver::forStylist($peluqueria);

        return view('peluquerias.edit', [
            'peluqueria' => $peluqueria,
            'stylistLabelSingular' => $stylistLabels['singular'],
            'stylistLabelPlural' => $stylistLabels['plural'],
        ]);
    }

    public function updateOwn(Request $request)
    {
        $peluqueria = auth()->user()->peluqueria;

        $data = $request->validate([
            'nombre' => 'required|string',
            'color' => 'nullable|string',
            'menu_color' => ['nullable', 'regex:/^#[0-9A-Fa-f]{6}$/'],
            'topbar_color' => ['nullable', 'regex:/^#[0-9A-Fa-f]{6}$/'],
            'msj_reserva_confirmada' => 'nullable|string',
            'msj_bienvenida' => 'nullable|string',
            'nit' => 'nullable|string',
            'direccion' => 'nullable|string',
            'municipio' => 'nullable|string',
            'trainer_label_singular' => 'nullable|string|max:191',
            'trainer_label_plural' => 'nullable|string|max:191',
        ]);

        $data['pos'] = $request->has('pos');
        $data['cuentaCobro'] = $request->has('cuentaCobro');
        $data['electronica'] = $request->has('electronica');

        $data['menu_color'] = $data['menu_color'] ?? null;
        $data['topbar_color'] = $data['topbar_color'] ?? null;

        $peluqueria->update($data);

        $this->syncStylistLabel(
            $peluqueria,
            $request->input('trainer_label_singular'),
            $request->input('trainer_label_plural')
        );

        return redirect()
            ->route('peluquerias.show', $peluqueria->id)
            ->with('success', 'Datos de tu peluqueria actualizados.');
    }

    public function update(Request $request, Peluqueria $peluqueria)
    {
        $data = $request->validate([
            'nombre' => 'required|string',
            'cuentaCobro' => 'nullable|boolean',
            'color' => 'nullable|string',
            'menu_color' => ['nullable', 'regex:/^#[0-9A-Fa-f]{6}$/'],
            'topbar_color' => ['nullable', 'regex:/^#[0-9A-Fa-f]{6}$/'],
            'msj_recordatorio' => 'nullable|string',
            'msj_bienvenida' => 'nullable|string',
            'nit' => 'nullable|string',
            'direccion' => 'nullable|string',
            'municipio' => 'nullable|string',
            'trainer_label_singular' => 'nullable|string|max:191',
            'trainer_label_plural' => 'nullable|string|max:191',
        ]);

        $data['pos'] = $request->has('pos');
        $data['cuentaCobro'] = $request->has('cuentaCobro');
        $data['electronica'] = $request->has('electronica');

        $data['menu_color'] = $data['menu_color'] ?? null;
        $data['topbar_color'] = $data['topbar_color'] ?? null;

        $peluqueria->update($data);

        $this->syncStylistLabel(
            $peluqueria,
            $request->input('trainer_label_singular'),
            $request->input('trainer_label_plural')
        );

        return redirect()->route('peluquerias.show', compact('peluqueria'));
    }

    public function showOwn()
    {
        return view('peluquerias.show');
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

<?php
namespace App\Http\Controllers;

use App\Models\Banco;
use App\Models\Proveedor;
use App\Models\Salida;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\Rule;

class SalidaController extends Controller
{
    public function index()
    {
        $salidas = Salida::with(['responsable', 'cuentaBancaria', 'tercero'])->get();

        return view('salidas.index', compact('salidas'));
    }

    public function create()
    {
        $salida = new Salida();
        $usuarios = User::all();
        $bancos = Banco::all();
        $proveedores = Proveedor::all();

        return view('salidas.create', compact('salida', 'usuarios', 'bancos', 'proveedores'));
    }

    public function store(Request $request)
    {
        $data = $this->validateSalida($request);

        Salida::create($this->prepareSalidaPayload($data));
        return redirect()->route('salidas.index')
                         ->with('success', 'Salida registrada correctamente.');
    }


    
    public function show(Salida $salida)
    {
        $salida->load(['responsable','cuentaBancaria','tercero']);
        return view('salidas.show', compact('salida'));
    }

    public function edit(Salida $salida)
    {
        $usuarios = User::all();
        $bancos   = Banco::all();
        $proveedores = Proveedor::all();
        return view('salidas.edit', compact('salida','usuarios','bancos','proveedores'));
    }

    public function update(Request $request, Salida $salida)
    {
        $data = $this->validateSalida($request);

        $salida->update($this->prepareSalidaPayload($data));
        return redirect()->route('salidas.index')
                         ->with('success', 'Salida actualizada.');
    }

    public function destroy(Salida $salida)
    {
        $salida->delete();
        return redirect()->route('salidas.index')
                         ->with('success', 'Salida eliminada.');
    }

    private function validateSalida(Request $request): array
    {
        $request->merge([
            'valor' => $request->input('valor') === null || $request->input('valor') === ''
                ? null
                : (int) preg_replace('/[^\d]/', '', (string) $request->input('valor')),
            'tercero_id' => $request->filled('tercero_id') ? $request->input('tercero_id') : null,
        ]);

        return $request->validate([
            'concepto'        => 'required|string|max:255',
            'fecha'           => 'required|date',
            'origen'          => 'required|in:caja,banco',
            'cuenta_bancaria' => [
                'required_if:origen,banco',
                'nullable',
                Rule::exists(Banco::class, 'id'),
            ],
            'valor'         => 'required|integer|min:0',
            'observaciones' => 'nullable|string',
            'tercero_id' => [
                'nullable',
                Rule::exists(Proveedor::class, 'id'),
            ],
        ]);
    }

    private function prepareSalidaPayload(array $data): array
    {
        $data['cuenta_bancaria'] = $data['origen'] === 'banco'
            ? $data['cuenta_bancaria']
            : null;

        $data['responsable'] = Auth::id() ?? $data['responsable'];

        $data['valor'] = (int) $data['valor'];

        unset($data['origen']);

        return $data;
    }
}

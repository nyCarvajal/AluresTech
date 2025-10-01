<?php
namespace App\Http\Controllers;

use App\Models\Banco;
use App\Models\Proveedor;
use App\Models\Salida;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

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
        $request->merge([
            'valor' => $request->input('valor') === null || $request->input('valor') === ''
                ? null
                : (int) preg_replace('/[^\d]/', '', (string) $request->input('valor')),
            'tercero_id' => $request->filled('tercero_id') ? $request->input('tercero_id') : null,
        ]);

        $data = $request->validate([
            'concepto'           => 'required|string|max:255',
            'fecha'              => 'required|date',
            'origen'             => 'required|in:caja,banco',
            'cuenta_bancaria' => 'required_if:origen,banco|nullable|exists:bancos,id',
            'valor'              => 'required|integer|min:0',
            'observaciones'      => 'nullable|string',
            'responsable'     => 'required|exists:usuarios,id',
            'tercero_id'         => 'nullable|exists:proveedors,id',
        ]);

        $data['cuenta_bancaria'] = $data['origen'] === 'banco'
            ? $data['cuenta_bancaria']
            : null;

        $data['responsable'] = Auth::id() ?? $data['responsable'];

        unset($data['origen']);

       $data['valor'] = (int) $data['valor'];

        Salida::create($data);
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
        $request->merge([
            'valor' => $request->input('valor') === null || $request->input('valor') === ''
                ? null
                : (int) preg_replace('/[^\d]/', '', (string) $request->input('valor')),
            'tercero_id' => $request->filled('tercero_id') ? $request->input('tercero_id') : null,
        ]);

        $data = $request->validate([
            'concepto'           => 'required|string|max:255',
            'fecha'              => 'required|date',
            'origen'             => 'required|in:caja,banco',
            'cuenta_bancaria' => 'required_if:origen,banco|nullable|exists:bancos,id',
            'valor'              => 'required|integer|min:0',
            'observaciones'      => 'nullable|string',
            'responsable'     => 'required|exists:usuarios,id',
            'tercero_id'         => 'nullable|exists:proveedors,id',
        ]);

        $data['cuenta_bancaria'] = $data['origen'] === 'banco'
            ? $data['cuenta_bancaria']
            : null;

        unset($data['origen']);

        $data['valor'] = (int) $data['valor'];

        $salida->update($data);
        return redirect()->route('salidas.index')
                         ->with('success', 'Salida actualizada.');
    }

    public function destroy(Salida $salida)
    {
        $salida->delete();
        return redirect()->route('salidas.index')
                         ->with('success', 'Salida eliminada.');
    }
}

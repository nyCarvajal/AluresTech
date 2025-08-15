<?php
namespace App\Http\Controllers;

use App\Models\Salida;
use App\Models\Banco;
use App\Models\Proveedor;
use App\Models\User;
use Illuminate\Http\Request;

class SalidaController extends Controller
{
    public function index()
    {
        $salidas = Salida::with(['responsable', 'cuentaBancaria', 'tercero'])->get();
        return view('salidas.index', compact('salidas'));
    }

    public function create()
    {
		 $salida      = new Salida();  
        $usuarios = User::all();
        $bancos   = Banco::all();
        $proveedores = Proveedor::all();
        return view('salidas.create', compact('salida', 'usuarios','bancos','proveedores'));
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'concepto'             => 'required|string|max:255',
            'fecha'                => 'required|date',
            'cuenta_bancaria_id'   => 'required|exists:bancos,id',
            'valor'                => 'required|numeric',
            'cuenta_contable'      => 'required|string|max:100',
            'observaciones'        => 'nullable|string',
            'responsable_id'       => 'required|exists:users,id',
            'tercero_id'           => 'required|exists:proveedors,id',
        ]);

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
        $data = $request->validate([
            'concepto'             => 'required|string|max:255',
            'fecha'                => 'required|date',
            'cuenta_bancaria_id'   => 'required|exists:bancos,id',
            'valor'                => 'required|numeric',
            'cuenta_contable'      => 'required|string|max:100',
            'observaciones'        => 'nullable|string',
            'responsable_id'       => 'required|exists:users,id',
            'tercero_id'           => 'required|exists:proveedors,id',
        ]);

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
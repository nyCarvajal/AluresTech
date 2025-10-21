<?php

namespace App\Http\Controllers;

use App\Models\Proveedor;
use App\Models\TipoIdentificacion;
use App\Models\Departamentos;
use App\Models\Municipios;
use Illuminate\Http\Request;

class ProveedorController extends Controller
{
    public function index()
    {
        $proveedores = Proveedor::with('TipoIdentificacion','municipio')->get();
        return view('proveedores.index', compact('proveedores'));
    }

    public function create()
    {
        $tiposDoc    = TipoIdentificacion::all();
        $departamentos = Departamentos::all();
        $municipios  = Municipios::all();
        $regimenOpciones = [1 => 'Persona Natural', 2 => 'Persona Jurídica'];
        return view('proveedores.create', compact('tiposDoc','departamentos','municipios','regimenOpciones'));
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'tipo_documento_id' => 'required|exists:tipo_identificacions,id',
            'numero_documento'  => 'required|string|unique:proveedores,numero_documento',
            'nombre'            => 'required|string|max:255',
            'regimen'           => 'required|in:1,2',
            'responsable_iva'   => 'nullable|in:0,1',
            'direccion'         => 'required|string|max:255',
            'municipio_id'      => 'required|exists:municipios,id',
        ]);
        $data['responsable_iva'] = $request->input('responsable_iva', 0);

        Proveedor::create($data);
        return redirect()->route('proveedores.index')->with('success','Proveedor creado.');
    }
	
	

    public function show($id)
    {
		 $proveedor = Proveedor::with(['tipoIdentificacion','municipio'])
                         ->findOrFail($id);
		 
        
        return view('proveedores.show', compact('proveedor'));
    }

    public function edit( $id)
    {
		 $proveedor = Proveedor::with(['tipoIdentificacion','municipio'])
                         ->findOrFail($id);
        $tiposDoc    = TipoIdentificacion::all();
        $departamentos = Departamentos::all();
        $municipios  = Municipios::all();
        $regimenOpciones = [1 => 'Persona Natural', 2 => 'Persona Jurídica'];
        return view('proveedores.edit', compact('proveedor','tiposDoc','departamentos','municipios','regimenOpciones'));
    }

    public function update(Request $request, Proveedor $proveedor)
    {
        $data = $request->validate([
            'tipo_documento_id' => 'required|exists:tipo_identificacions,id',
            'numero_documento'  => "required|string|unique:proveedores,numero_documento,{$proveedor->id}",
            'nombre'            => 'required|string|max:255',
            'regimen'           => 'required|in:1,2',
            'responsable_iva'   => 'nullable|in:0,1',
            'direccion'         => 'required|string|max:255',
            'municipio_id'      => 'required|exists:municipios,id',
        ]);
         $data['responsable_iva'] = $request->input('responsable_iva', 0);

        $proveedor->update($data);
        return redirect()->route('proveedores.index')->with('success','Proveedor actualizado.');
    }

    public function destroy(Proveedor $proveedor)
    {
        $proveedor->delete();
        return redirect()->route('proveedores.index')->with('success','Proveedor eliminado.');
    }
}
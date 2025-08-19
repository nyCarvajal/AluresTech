<?php

namespace App\Http\Controllers;
use Illuminate\Support\Facades\Auth;
use App\Models\Cliente;
use App\Models\Peluqueria;
use Illuminate\Support\Facades\DB;
use App\Models\TipoIdentificacion;
use App\Models\Paises;
use App\Models\TipoUsuario;
use App\Models\Nivel;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

use App\Notifications\OneMsgTemplateNotification;
use CloudinaryLabs\CloudinaryLaravel\Facades\Cloudinary;

class ClientesController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
		
        $clientes = Cliente::with([
        'nivel',
        'pais',
        'departamento',
        'municipio',
    ])->get(); // o paginate()
    return view('clientes.index', compact('clientes'));
    }

    /**
     * Show the form for creating a new resource.
     */
    
	public function create()
{
    $tipoIdentificaciones = TipoIdentificacion::all();
    $paises               = Paises::orderBy('nombre')->get();
	$niveles			  = Nivel::all();

    // Encuentra Colombia (ajusta el campo que uses para el nombre)
    $colombia = $paises->firstWhere('nombre', 'Colombia');
    $defaultPais = $colombia ? $colombia->id : $paises->pluck('id')->first();

    return view('clientes.create', compact(
        'tipoIdentificaciones',
        'paises',
        'defaultPais',
		'niveles'
    ));
}


    /**
     * Store a newly created resource in storage.
     */
   
	public function store(Request $request)
{
    $data = $request->validate([
		'tipo_identificacion'    => 'required|exists:tipo_identificacions,id',
        'numero_identificacion'  => 'required|string|max:50',
        'nombres'                => 'required|string|max:255',
        'apellidos'              => 'nullable|string|max:255',
        'correo'                 => 'nullable|email|unique:clientes,correo',
        'whatsapp'               => 'nullable|string',
        'fecha_nacimiento'       => 'nullable|date',
        'direccion'              => 'nullable|string',
        'pais'                   => 'nullable|exists:paises,id',
        'departamento'           => 'nullable|exists:departamentos,id',
        'municipio'              => 'nullable|exists:municipios,id',
        'sexo'                   => 'nullable|in:M,F',
		'nivel_id'				 => 'nullable|exists:nivels,id',
		'foto'       			 => 'nullable|image|mimes:jpeg,png,jpg,gif,webp|max:10240',
      
    ]);
	
	if ( $request->hasFile('foto') ) {
    // invocamos la API de subida
    $uploadResult = Cloudinary::uploadApi()
        ->upload(
            $request->file('foto')->getRealPath(),
            ['folder' => 'players']
        );

    // $uploadResult es un array: extraemos el public_id
    $data['foto'] = $uploadResult['public_id'];
}

// 3) Construir mensaje dinámico desde la BD 
    $peluqueria    = Auth::user()->peluqueria; // o where('id', …)
    $texto   = $peluqueria->msj_bienvenida;

    // 4) Enviar por WhatsApp (se va a la cola) 
    $payload = [
	'0'   =>$texto,
    '1'   =>$peluqueria->nombre,               // {{1}}
];


   $cliente= Cliente::create($data);
	 DB::connection('mysql')->table('clientes')->insert($data);
	
	
	if ($data && $data['whatsapp']) {
        $cliente->notify(new OneMsgTemplateNotification('bienvenida', array_merge(
            $payload,
            ['nombre' => $cliente->nombre]  // por si tu plantilla incluye {{nombre}}
        )));
    }

    return redirect()
           ->route('clientes.index')
           ->with('success', 'Cliente creado correctamente.');
}


    /**
     * Display the specified resource.
     */
    public function show(Cliente $cliente)
{
	// Trae las últimas 10 reservas (ordenadas por fecha descendente)
    $reservas = $cliente
        ->clases()              // relación hasMany Reserva en el modelo Cliente
        ->orderBy('fecha', 'desc')
        ->take(10)
        ->get();
		
		
		
	 // Trae la última membresía comprada (suponiendo relación hasMany Membresia)
    $ultimaMembresia = $cliente
          
        ->membresiasClientes()
		->with('paquete')		// relación hasMany Membresia en el modelo Cliente
        ->orderBy('id', 'desc')
		->where('estado', 1)
        ->first();
		
		$clases =0;
		$clasesVistas=0;
		$numReservas=0;
		$res=0;
		
		if($ultimaMembresia){
		$clases       = $ultimaMembresia->clases ?? 0;
$clasesVistas = $ultimaMembresia->clasesVistas ?? 0;   // o lo que sea tu lógica
$numReservas  = $ultimaMembresia->numReservas;
$res=$ultimaMembresia->reservas;
		}
    return view('clientes.view', compact('clases','clasesVistas','numReservas', 'cliente', 'reservas', 'ultimaMembresia', 'res'));
}


    /**
     * Muestra el formulario de edición.
     */
   public function edit(Cliente $cliente)
{
    $tipoIdentificaciones = TipoIdentificacion::all();
    $paises               = Paises::orderBy('nombre')->get();
	$tipos = TipoUsuario::all();
    $niveles              = Nivel::orderBy('nivel')->get();  // <-- añadir
	 // Encuentra Colombia (ajusta el campo que uses para el nombre)
    $colombia = $paises->firstWhere('nombre', 'Colombia');
    $defaultPais = $colombia ? $colombia->id : $paises->pluck('id')->first();

    return view('clientes.edit', compact(
        'cliente',
        'tipoIdentificaciones',
        'paises',
		'tipos',
		'defaultPais',
        'niveles'  // <-- añadir
    ));
}


    /**
     * Procesa la actualización del cliente.
     */
    public function update(Request $request, Cliente $cliente)
    {
        // Validación (ajusta reglas según tus columnas)
        $data = $request->validate([
            'tipo_identificacion'   => 'required|exists:tipo_identificacions,id',
            'numero_identificacion' => 'required|string|max:50',
            'nombres'               => 'required|string|max:255',
            'apellidos'             => 'required|string|max:255',
            'correo'                => "required|email|unique:clientes,correo,{$cliente->id}",
            'whatsapp'              => 'required|string',
            'fecha_nacimiento'      => 'required|date',
            'direccion'             => 'nullable|string',
            'pais'                  => 'required|exists:paises,id',
            'departamento'          => 'required|exists:departamentos,id',
            'municipio'             => 'required|exists:municipios,id',
            'sexo'                  => 'required|in:M,F',
			'nivel_id'				=> 'nullable|exists:nivels,id',
			'foto'       			 => 'nullable|image|mimes:jpeg,png,jpg,gif,webp|max:10240',
      
        ]);
		
			
	if ( $request->hasFile('foto') ) {
    // invocamos la API de subida
    $uploadResult = Cloudinary::uploadApi()
        ->upload(
            $request->file('foto')->getRealPath(),
            ['folder' => 'players']
        );

    // $uploadResult es un array: extraemos el public_id
    $data['foto'] = $uploadResult['public_id'];
}

        // Actualiza y redirige
        $cliente->update($data);

        return redirect()
               ->route('clientes.index')
               ->with('success', 'Cliente actualizado correctamente.');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Cliente $cliente)
    {
        //
    }
	


    public function search(Request $request)
    {
        $q = $request->input('q', '');

        // Devuelve id y nombre completo (máx. 20 resultados)
        $clientes= Cliente::when($q, function ($query) use ($q) {
                    $query->whereRaw("CONCAT(nombres,' ',apellidos) LIKE ?", ["%{$q}%"]);
                })
                ->selectRaw("id, CONCAT(nombres,' ',apellidos) AS nombre")
                ->orderBy('nombre')
                ->limit(20)
                ->get();
    
	
	 return response()->json($clientes);   

	}
}

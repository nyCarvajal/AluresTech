<?php

namespace App\Http\Controllers;
use Illuminate\Support\Facades\Auth;
use App\Models\Cliente;
use App\Models\Peluqueria;
use Illuminate\Support\Facades\DB;
use App\Models\TipoIdentificacion;
use App\Models\Paises;
use App\Models\TipoUsuario;
use App\Models\OrdenDeCompra;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

use App\Notifications\OneMsgTemplateNotification;
use CloudinaryLabs\CloudinaryLaravel\Facades\Cloudinary;
use Carbon\Carbon;

class ClientesController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $q = $request->input('q');

        $clientes = Cliente::with(['pais', 'departamento', 'municipio'])
            ->when($q, function ($query) use ($q) {
                $query->where(function ($sub) use ($q) {
                    $sub->where('nombres', 'like', "%{$q}%")
                        ->orWhere('apellidos', 'like', "%{$q}%")
                        ->orWhere('numero_identificacion', 'like', "%{$q}%")
                        ->orWhere('correo', 'like', "%{$q}%")
                        ->orWhere('whatsapp', 'like', "%{$q}%");
                });
            })
            ->get();

        return view('clientes.index', compact('clientes'));
    }

    public function reengage()
    {
        $threshold = Carbon::now()->subMonth();

        $clientes = Cliente::with(['reservas' => function ($query) {
                $query->latest('fecha')->limit(1);
            }])
            ->whereDoesntHave('reservas', function ($query) use ($threshold) {
                $query->where('fecha', '>=', $threshold);
            })
            ->orderBy('nombres')
            ->orderBy('apellidos')
            ->get();

        $mensajeBase = optional(optional(Auth::user())->peluqueria)->msj_recordatorio ??
            'Hola {{nombre}}, tenemos disponibilidad hoy. ¿Te gustaría agendar tu próxima cita?';

        return view('clientes.reengage', [
            'clientes' => $clientes,
            'mensajeBase' => $mensajeBase,
            'threshold' => $threshold,
        ]);
    }

    public function birthdays()
    {
        $today = Carbon::today();

        $clientes = Cliente::with(['reservas' => function ($query) {
                $query->latest('fecha')->limit(1);
            }])
            ->whereNotNull('fecha_nacimiento')
            ->whereMonth('fecha_nacimiento', $today->month)
            ->whereDay('fecha_nacimiento', $today->day)
            ->orderBy('nombres')
            ->orderBy('apellidos')
            ->get();

        return view('clientes.birthdays', [
            'clientes' => $clientes,
            'today' => $today,
        ]);
    }

    /**
     * Show the form for creating a new resource.
     */
    
	public function create()
{
    $tipoIdentificaciones = TipoIdentificacion::all();
    $paises               = Paises::orderBy('nombre')->get();


    // Encuentra Colombia (ajusta el campo que uses para el nombre)
    $colombia = $paises->firstWhere('nombre', 'Colombia');
    $defaultPais = $colombia ? $colombia->id : $paises->pluck('id')->first();

    return view('clientes.create', compact(
        'tipoIdentificaciones',
        'paises',
        'defaultPais',
		
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
        $reservas = $cliente->reservas()
            ->with('entrenador')
            ->orderBy('fecha', 'desc')
            ->take(10)
            ->get();

        $transacciones = OrdenDeCompra::where('cliente', $cliente->id)
            ->with('ventas')
            ->orderBy('fecha_hora', 'desc')
            ->take(10)
            ->get();

        return view('clientes.view', compact('cliente', 'reservas', 'transacciones'));
    }


    /**
     * Muestra el formulario de edición.
     */
   public function edit(Cliente $cliente)
{
    $tipoIdentificaciones = TipoIdentificacion::all();
    $paises               = Paises::orderBy('nombre')->get();
	$tipos = TipoUsuario::all();
   // Encuentra Colombia (ajusta el campo que uses para el nombre)
    $colombia = $paises->firstWhere('nombre', 'Colombia');
    $defaultPais = $colombia ? $colombia->id : $paises->pluck('id')->first();

    return view('clientes.edit', compact(
        'cliente',
        'tipoIdentificaciones',
        'paises',
		'tipos',
		'defaultPais',
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

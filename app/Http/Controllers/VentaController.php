<?php

namespace App\Http\Controllers;

use App\Notifications\WhatsAppTextMessageNotification;
use App\Models\Venta;
use App\Models\Alumno;    
use App\Models\Pago;    
use App\Models\Item;
use App\Models\Membresia;
use App\Models\MembresiaAlumno;
use App\Models\OrdenDeCompra; 
use App\Models\Banco;
use App\Models\Club;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Notifications\OneMsgTemplateNotification;
use Carbon\Carbon;

class VentaController extends Controller
{
	
	public function storememb(Request $request)
{
    $data = $request->validate([
        'jugador_id'   => 'required|exists:alumnos,id',
        'membresia_id' => 'required|exists:membresias,id',
    ]);
	 $membresia = Membresia::findOrFail($data['membresia_id']);

    // 1) Crear la orden de compra
    $orden = OrdenDeCompra::create([
        'cliente' => $data['jugador_id'],
		'fecha_hora'=>now(),  
		'responsable' => auth()->id(),   
        // …otros campos…
    ]);

    // 2) Crear la venta
    $venta = Venta::create([
        'cuenta' => $orden->id,
		'producto'=>$membresia->item,
		'valor_unitario'=> $membresia->valor,
		'valor_total'=> $membresia->valor,
        // …otros campos de venta…
    ]);


    // 4) Insertar en membresia_alumno
    $jugador = Alumno::findOrFail($data['jugador_id']);
    $jugador->membresias()->attach(
        $membresia->id,
        [
            'clases'   => $membresia->clases,
            'reservas' => $membresia->reservas,
			'estado'   => 1,
        ]
    );
	
	 /* 3) Construir mensaje dinámico desde la BD */
    $club    = Club::first();  // o where('id', …)
  
  if(!is_null($membresia->clases)){

    // 4) Enviar por WhatsApp (se va a la cola) 
    $payload = [
    '0'   =>$membresia->clases,
    '1'   =>'clases',               // {{1}}
	'2'	  =>"mensual",
	'3'   =>"https://wa.me/{$club->telefono}?text=Hola",
	
];
  }else if(!is_null($membresia->reservas)){
	  
	   $payload = [
    '0'   =>$membresia->reservas,
    '1'   =>'reservas',               // {{1}}
	'2'	  =>"Mensual",
	'3'   =>"https://wa.me/{$club->telefono}?text=Hola",
	
];
	  
  }

if ($jugador && $jugador->whatsapp) {
        $jugador->notify(new OneMsgTemplateNotification('paquete', array_merge(
            $payload,
            ['nombre' => $jugador->nombre]  // por si tu plantilla incluye {{nombre}}
        )));
    }

  
	

    // 5) Redirigir a ventas.index con alumno_id y cuenta (orden_de_compra)
    return redirect()
           ->route('ventas.index', [
               'alumno_id' => $data['jugador_id'],
               'orden_id'    => $orden->id,
           ]);
}

 /** 1) Mostrar el formulario de relación */
    public function relacion()
    {
        $jugadores  = Alumno::all();
        $membresias = Membresia::all();

        return view('ventas.relacion', compact('jugadores', 'membresias'));
    }
	
	
	
     public function index(Request $request)
    {
        // 1) Obtenemos todos los alumnos para el selector
        $alumnos = Alumno::orderBy('nombres')->get();

        // 2) Si nos llega ?alumno_id=XX y NO viene ?orden_id, creamos una nueva orden_de_compra
        $alumnoSeleccionado  = null;
        $ordenSeleccionada   = null;
        $alumnoId            = $request->query('alumno_id', null);
        $ordenId             = $request->query('orden_id', null);
		 $orden  = OrdenDeCompra::find($ordenId);
		if(! $ordenId){
			
		
        if ($alumnoId) {
            // 2.a) Buscamos el alumno
            $alumnoSeleccionado = Alumno::find($alumnoId);
            if ($alumnoSeleccionado) {
                // 2.b) Creamos la orden para ese alumno
                $orden = OrdenDeCompra::create([
                    'fecha_hora'  => Carbon::now(),
                    'responsable' => auth()->id(),
                    'cliente'     => $alumnoId,
                    'activa'      => true,
                ]);

               
            }
        }else{
			 $orden = OrdenDeCompra::create([
                    'fecha_hora'  => Carbon::now(),
                    'responsable' => auth()->id(),
					//'cliente'     => $alumnoSeleccionado,
                    'activa'      => true,
                ]);

               
		}
		}
		
		 $totalVentas = 0;
    $totalPagos  = 0;
    $resta       = 0;
    if ($ordenId) {
        $totalVentas = Venta::where('cuenta', $ordenId)->sum('valor_total');
        $totalPagos  = Pago::where('cuenta',   $ordenId)->sum('valor');
        $resta       = $totalVentas - $totalPagos;
    }

        // 3) Si ya viene ?alumno_id=XX&orden_id=YY, cargamos el alumno y la orden
        if ($alumnoId && $ordenId) {
            $alumnoSeleccionado = Alumno::find($alumnoId);
            $orden  = OrdenDeCompra::find($ordenId);
        }

        // 4) Lista de ítems para el selector (producto)
        $items = Item::orderBy('nombre')->get();

        // 5) Ventas paginadas
        $ventas = Venta::where('cuenta', $ordenId)
               ->orderBy('id', 'desc')
               ->paginate(10);
        // 6) Si existe orden_id, obtenemos los pagos asociados (campo `cuenta = orden_id`)
        if ($ordenId) {
            $pagos = Pago::where('cuenta', $ordenId)
                         ->orderBy('fecha_hora', 'desc')
                         ->get();
        } else {
            $pagos = collect();
        }

        // 7) Bancos para el modal de pago
        $banks = Banco::all();
		
		 $clubId = auth()->user()->club_id; 
		$usuarios = User::query()
        ->where('club_id', $clubId)
        ->orderBy('nombre')
        ->get();
        return view('ventas.index', [
            'alumnos'             => $alumnos,
			'usuarios'            => $usuarios,
            'alumnoSeleccionado'  => $alumnoSeleccionado,
            'ordenes'             => null,   // ya no necesitamos listar ordenes aquí
            'ordenSeleccionada'   => $orden,
            'items'               => $items,
            'ventas'              => $ventas,
            'cuentaSeleccionada'  => $ordenId, // para filtrar pagos
            'pagos'               => $pagos,
            'banks'               => $banks,
			'totalVentas' => $totalVentas,
			'totalPagos'  => $totalPagos,
			'resta'       => $resta,
        ]);
    }
	
	
	
	public function totales(OrdenDeCompra $orden)
{
    $totalVentas = Venta::where('cuenta', $orden->id)->sum('valor_total');
    $totalPagos  = Pago::where('cuenta',   $orden->id)->sum('valor');
    return response()->json([
        'totalVentas'=> $totalVentas,
        'totalPagos' => $totalPagos,
        'resta'      => $totalVentas - $totalPagos,
    ]);
}
	
	
	
	 /**
     * storeByItem: crea una venta usando:
     *  - orden_de_compra_id  (en lugar de alumno_id)
     *  - item_id
     */
    public function storeByItem(Request $request)
    {
        $request->validate([
            'orden_de_compra_id' => 'required|exists:orden_de_compras,id',
            'item_id'            => 'required|exists:items,id',
        ]);

        // 1) Obtenemos la orden y el ítem
        $orden = OrdenDeCompra::findOrFail($request->input('orden_de_compra_id'));
        $item  = Item::findOrFail($request->input('item_id'));

        // 2) Construimos los datos de la venta
        $datosVenta = [
            // 'cuenta' = la ID de la orden de compra
            'cuenta'        => $orden->id,
            // 'producto' = el ID del ítem
            'producto'      => $item->id,
            'cantidad'      => 1,
            'descuento'     => 0.00,
            'valor_unitario'=> $item->valor,
            'valor_total'   => $item->valor,
        ];

        Venta::create($datosVenta);

        // 3) Redirigimos de vuelta a index, conservando ?orden_id=XX
        return redirect()
            ->route('ventas.index', ['orden_id' => $orden->id, 'alumno_id'=>$orden->cliente])
            ->with('success', 'Venta creada correctamente.');
    }


    public function create()
    {
        return view('ventas.create');
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'cuenta'         => 'required|string|max:255',
            'producto'       => 'required|string|max:255',
            'cantidad'       => 'required|integer|min:1',
            'descuento'      => 'nullable|numeric|min:0',
            'valor_unitario' => 'required|numeric|min:0',
            'valor_total'    => 'required|numeric|min:0',
        ]);

        Venta::create($data);

        return redirect()->route('ventas.index')
                         ->with('success', 'Venta creada correctamente.');
    }

    public function show(Venta $venta)
    {
        return view('ventas.show', compact('venta'));
    }

    public function edit(Venta $venta)
    {
        return view('ventas.edit', compact('venta'));
    }

   public function update(Request $request, Venta $venta)
{
	
    // Validación de los campos que vienen del formulario
    $data = $request->validate([
        'cantidad'       => 'required|integer|min:1',
        'descuento'      => 'nullable|numeric|min:0',
        'valor_unitario' => 'nullable|numeric|min:0',
		'porcentajeComision'  => ['nullable','numeric','min:0','max:100'],
		'usuario_id'  => ['nullable','exists:alumnos,id'],
    ]);

    // Si tu tabla tiene un campo valor_total, lo calculamos:
   $venta->fill($request->only('cantidad','descuento','valor_unitario', 'usuario_id'));

    $descuento    = (float) $venta->descuento;
    $unitBase     = (float) $venta->valor_unitario;
    $unitNeto     = round($unitBase * (1 - ($descuento/100)), 2);
    $venta->valor_total = round($unitNeto * (int) $venta->cantidad, 2);

    if($request->filled('porcentajeComision')){
        $venta->porcentajeComision = $request->input('porcentajeComision');
        $venta->comision = round($venta->valor_total *  ($venta->porcentajeComision/100), 2);
    } else {
        $venta->porcentajeComision = null;
        $venta->comision = null;
    }

    // Actualizamos la venta
    $venta->save();
	
    // Recuperamos los parámetros para el redirect
    $ordenId  = $venta->cuenta;
$alumnoId = $venta->orden->cliente;

    // Redirigimos incluyendo los parámetros para volver al listado
    return redirect()
        ->route('ventas.index', [
            'orden_id'  => $ordenId,
            'alumno_id' => $alumnoId,
        ])
        ->with('success', 'Venta actualizada correctamente.');
}


   public function destroy($id)
{
    // Elimina la venta
    Venta::findOrFail($id)->delete();

    // Recupera los parámetros de la URL
    $ordenId   = request()->query('orden_id');
    $alumnoId  = request()->query('alumno_id');

    // Redirige a la ruta de ventas con esos parámetros
    return redirect()
        ->route('ventas.index', [
            'orden_id'  => $ordenId,
            'alumno_id' => $alumnoId,
        ])
        ->with('success', 'Venta eliminada correctamente.');
}

	
	


}

<?php

namespace App\Http\Controllers;

use App\Notifications\WhatsAppTextMessageNotification;
use App\Models\Venta;
use App\Models\Cliente;    
use App\Models\Pago;
use App\Models\Item;
use App\Models\InventarioHistorial;
use App\Models\Membresia;
use App\Models\MembresiaCliente;
use App\Models\OrdenDeCompra; 
use App\Models\Banco;
use App\Models\Peluqueria;
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
        'jugador_id'   => 'required|exists:clientes,id',
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


    // 4) Insertar en membresia_cliente
    $jugador = Cliente::findOrFail($data['jugador_id']);
    $jugador->membresias()->attach(
        $membresia->id,
        [
            'clases'   => $membresia->clases,
            'reservas' => $membresia->reservas,
			'estado'   => 1,
        ]
    );
	
	 /* 3) Construir mensaje dinámico desde la BD */
    $peluqueria    = Peluqueria::first();  // o where('id', …)
  
  if(!is_null($membresia->clases)){

    // 4) Enviar por WhatsApp (se va a la cola) 
    $payload = [
    '0'   =>$membresia->clases,
    '1'   =>'clases',               // {{1}}
	'2'	  =>"mensual",
	'3'   =>"https://wa.me/{$peluqueria->telefono}?text=Hola",
	
];
  }else if(!is_null($membresia->reservas)){
	  
	   $payload = [
    '0'   =>$membresia->reservas,
    '1'   =>'reservas',               // {{1}}
	'2'	  =>"Mensual",
	'3'   =>"https://wa.me/{$peluqueria->telefono}?text=Hola",
	
];
	  
  }

if ($jugador && $jugador->whatsapp) {
        $jugador->notify(new OneMsgTemplateNotification('paquete', array_merge(
            $payload,
            ['nombre' => $jugador->nombre]  // por si tu plantilla incluye {{nombre}}
        )));
    }

  
	

    // 5) Redirigir a ventas.index con cliente_id y cuenta (orden_de_compra)
    return redirect()
           ->route('ventas.index', [
               'cliente_id' => $data['jugador_id'],
               'orden_id'    => $orden->id,
           ]);
}

 /** 1) Mostrar el formulario de relación */
    public function relacion()
    {
        $jugadores  = Cliente::all();
        $membresias = Membresia::all();

        return view('ventas.relacion', compact('jugadores', 'membresias'));
    }
	
	
	
     public function index(Request $request)
    {
        // 1) Obtenemos todos los clientes para el selector
        $clientes = Cliente::orderBy('nombres')->get();

        // 2) Si nos llega ?cliente_id=XX y NO viene ?orden_id, creamos una nueva orden_de_compra
        $clienteSeleccionado  = null;
        $ordenSeleccionada   = null;
        $clienteId            = $request->query('cliente_id', null);
        $ordenId             = $request->query('orden_id', null);
		 $orden  = OrdenDeCompra::find($ordenId);
		if(! $ordenId){
			
		
        if ($clienteId) {
            // 2.a) Buscamos el cliente
            $clienteSeleccionado = Cliente::find($clienteId);
            if ($clienteSeleccionado) {
                // 2.b) Creamos la orden para ese cliente
                $orden = OrdenDeCompra::create([
                    'fecha_hora'  => Carbon::now(),
                    'responsable' => auth()->id(),
                    'cliente'     => $clienteId,
                    'activa'      => true,
                ]);

               
            }
        }else{
			 $orden = OrdenDeCompra::create([
                    'fecha_hora'  => Carbon::now(),
                    'responsable' => auth()->id(),
					//'cliente'     => $clienteSeleccionado,
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

        // 3) Si ya viene ?cliente_id=XX&orden_id=YY, cargamos el cliente y la orden
        if ($clienteId && $ordenId) {
            $clienteSeleccionado = Cliente::find($clienteId);
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
		
		 $peluqueriaId = auth()->user()->peluqueria_id; 
		$usuarios = User::query()
        ->where('peluqueria_id', $peluqueriaId)
        ->orderBy('nombre')
        ->get();
        return view('ventas.index', [
            'clientes'             => $clientes,
			'usuarios'            => $usuarios,
            'clienteSeleccionado'  => $clienteSeleccionado,
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
     *  - orden_de_compra_id  (en lugar de cliente_id)
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

        $venta = Venta::create($datosVenta);

        if ($item->tipo == 1) {
            $item->decrement('cantidad', 1);
            InventarioHistorial::create([
                'item_id' => $item->id,
                'cambio' => -1,
                'descripcion' => 'Venta #' . $venta->id,
            ]);
        }

        // 3) Redirigimos de vuelta a index, conservando ?orden_id=XX
        return redirect()
            ->route('ventas.index', ['orden_id' => $orden->id, 'cliente_id'=>$orden->cliente])
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

        $venta = Venta::create($data);

        $item = Item::find($venta->producto);
        if ($item && $item->tipo == 1) {
            $item->decrement('cantidad', $venta->cantidad);
            InventarioHistorial::create([
                'item_id' => $item->id,
                'cambio' => -$venta->cantidad,
                'descripcion' => 'Venta #' . $venta->id,
            ]);
        }

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
		'porcentaje_comision'  => ['nullable','numeric','min:0','max:100'],
		'usuario_id'  => ['nullable','exists:clientes,id'],
    ]);

    // Si tu tabla tiene un campo valor_total, lo calculamos:
   $venta->fill($request->only('cantidad','descuento','valor_unitario', 'usuario_id'));

    $descuento    = (float) $venta->descuento;
    $unitBase     = (float) $venta->valor_unitario;
    $unitNeto     = round($unitBase * (1 - ($descuento/100)), 2);
    $venta->valor_total = round($unitNeto * (int) $venta->cantidad, 2);

    if($request->filled('porcentaje_comision')){
        $venta->porcentaje_comision = $request->input('porcentaje_comision');
        $venta->comision = round($venta->valor_total *  ($venta->porcentaje_comision/100), 2);
    } else {
        $venta->porcentaje_comision = null;
        $venta->comision = null;
    }

    // Actualizamos la venta
    $venta->save();
	
    // Recuperamos los parámetros para el redirect
    $ordenId  = $venta->cuenta;
$clienteId = $venta->orden->cliente;

    // Redirigimos incluyendo los parámetros para volver al listado
    return redirect()
        ->route('ventas.index', [
            'orden_id'  => $ordenId,
            'cliente_id' => $clienteId,
        ])
        ->with('success', 'Venta actualizada correctamente.');
}


   public function destroy($id)
{
    // Elimina la venta
    Venta::findOrFail($id)->delete();

    // Recupera los parámetros de la URL
    $ordenId   = request()->query('orden_id');
    $clienteId  = request()->query('cliente_id');

    // Redirige a la ruta de ventas con esos parámetros
    return redirect()
        ->route('ventas.index', [
            'orden_id'  => $ordenId,
            'cliente_id' => $clienteId,
        ])
        ->with('success', 'Venta eliminada correctamente.');
}

	
	


}

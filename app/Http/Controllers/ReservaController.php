<?php

namespace App\Http\Controllers;
use Illuminate\Support\Facades\Auth;

use App\Models\Reserva;
use App\Models\User;
use App\Models\clase;
use App\Models\Peluqueria;
use App\Models\Cancha; 
use App\Models\Cliente;
use App\Models\Tipocita;
use App\Models\Item;
use App\Models\OrdenDeCompra;
use App\Models\Venta;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use Carbon\Carbon; 
use Illuminate\Support\Facades\Validator;
use App\Notifications\OneMsgTemplateNotification;
use Illuminate\Support\Facades\Log;
use App\Support\RoleLabelResolver;
use Illuminate\Support\Facades\DB;
use Throwable;


class ReservaController extends Controller
{
	
        public function calendar()
{
    $entrenadores = User::all(); // o tu filtro de usuarios con rol “entrenador”

    $tipocitas    = Tipocita::all();
    $servicios    = Item::where('tipo', '!=', 1)
        ->orderBy('nombre')
        ->get();
    $labels = RoleLabelResolver::forStylist();

    return view('reservas.calendar', [
        'entrenadores' => $entrenadores,
        'tipocitas' => $tipocitas,
        'servicios' => $servicios,
        'stylistLabelSingular' => $labels['singular'],
        'stylistLabelPlural' => $labels['plural'],
    ]);


}

 public function horario(Request $request)
    {
        $date = $request->input('date', Carbon::today()->toDateString());
        $prevDate = Carbon::parse($date)->subDay()->toDateString();
        $nextDate = Carbon::parse($date)->addDay()->toDateString();
                 $entrenadores = User::all(); // o tu filtro de usuarios con rol “entrenador”

        $canchas = Cancha::all();
        $canchaIds = $canchas->pluck('id')->toArray();

        $startOfDay = Carbon::parse("{$date} 06:00");
        $endOfDay   = Carbon::parse("{$date} 22:00");

        // Generar slots de 30 minutos
        $timeslots = [];
        for ($time = $startOfDay->copy(); $time->lte($endOfDay); $time->addMinutes(30)) {
            $timeslots[] = $time->format('H:i:s');
        }

        $reservas = Reserva::whereDate('fecha', $date)
            ->whereIn('cancha_id', $canchaIds)
                        ->where('estado', '<>', 'Cancelada')
            ->get();

        // Inicializar eventos
        $events = [];
        foreach ($canchas as $cancha) {
            foreach ($timeslots as $slot) {
                $events[$cancha->id][$slot] = null;
            }
        }

        // Mapear reservas en todos los slots que dura
        foreach ($reservas as $reserva) {
            $time = Carbon::parse($reserva->fecha);
            $minutesFromStart = $startOfDay->diffInMinutes($time);
            if ($minutesFromStart < 0 || $minutesFromStart > $startOfDay->diffInMinutes($endOfDay)) {
                continue;
            }
            $startIndex = intdiv($minutesFromStart, 30);
            $spanSlots = (int) ceil(($reserva->duracion ?? $reserva->duration) / 30);

            for ($i = 0; $i < $spanSlots; $i++) {
                $index = $startIndex + $i;
                if (isset($timeslots[$index])) {
                    $slotKey = $timeslots[$index];
                    $events[$reserva->cancha_id][$slotKey] = $reserva;
                }
            }
        }

        $labels = RoleLabelResolver::forStylist();

        return view('reservas.horario', array_merge(
            compact('canchas', 'timeslots', 'events', 'prevDate', 'nextDate', 'entrenadores'),
            [
                'stylistLabelSingular' => $labels['singular'],
                'stylistLabelPlural' => $labels['plural'],
            ]
        ));
    }




	public function events(Request $request)
{
    // 1) Trae y filtra la consulta (mejor en base de datos, no en colección)
    $estadosVisibles = ['Confirmada', 'Pendiente', 'No Asistida', 'cobrada'];

    $query = Reserva::with(['cliente', 'entrenador', 'servicio', 'orden'])
        ->whereIn('estado', $estadosVisibles);
    if ($request->filled('cancha_id')) {
        $query->where('cancha_id', $request->cancha_id);
    }
    if ($request->filled('entrenador_id')) {
        $query->where('entrenador_id', $request->entrenador_id);
    }
    $query->where('estado', '<>', 'Cancelada');
    $reservas = $query->get();
	
	  

    // 2) Mapea y reindexa con values()
    $eventos = $reservas->map(function($r) {
        $start    = Carbon::parse($r->fecha)->toIso8601String();
        $end      = Carbon::parse($r->fecha)
                          ->addMinutes($r->duracion)
                          ->toIso8601String();

        $color = optional($r->entrenador)->color ?? '#6042F5';
        $textColor = '#121212';

        if ($r->estado === 'No Asistida') {
            $color = '#0d6efd';
            $textColor = '#ffffff';
        } elseif ($r->estado === 'Pendiente') {
            $color = '#ffc107';
            $textColor = '#212529';
        }

        $clienteNombre = trim(collect([
            optional($r->cliente)->nombres,
            optional($r->cliente)->apellidos,
        ])->filter()->implode(' '));
        $servicioNombre = optional($r->servicio)->nombre;
        $titleParts = array_filter([
            $clienteNombre,
            $servicioNombre,
        ]);
        $title = count($titleParts)
            ? implode("\n", $titleParts)
            : ($servicioNombre ?: $clienteNombre ?: 'Reserva');

        $base = [
            'id'              => $r->id,
            'start'           => $start,
            'end'             => $end,
            'type'            => $r->tipo,
            'status'          => $r->estado,
            'duration'        => $r->duracion,
            'title'           => $title,
            'borderColor'     => $color,
            'textColor'       => $textColor,
            'backgroundColor' => $color,
            'extendedProps'   => [
                'tipo'          => $r->tipo,              // reserva | torneo | clase
                'estado'        => $r->estado,            // confirmada | pendiente
                'entrenador_id' => $r->entrenador_id,
                'cliente_id'    => $r->cliente_id,
                'servicio_id'   => $r->servicio_id,
                'servicio_nombre' => $servicioNombre,
                'orden_id'      => $r->orden_id,
                'venta_id'      => $r->venta_id,
                'cuenta_url'    => $r->orden_id
                    ? route('ventas.index', [
                        'cliente_id' => $r->cliente_id,
                        'orden_id'   => $r->orden_id,
                    ])
                    : null,
                'cuenta_label'  => $r->orden_id ? 'Cuenta #' . $r->orden_id : null,
            ],
            'cancha_id'       => $r->cancha_id,
            'entrenador_id'   => $r->entrenador_id,
            'cliente_id'      => $r->cliente_id,
            'orden_id'        => $r->orden_id,
        ];

      

        return $base;
    })
    ->values();  // ← aquí reindexas para que el JSON sea un array

    return response()->json($eventos);
}
    /**
     * Marca una reserva como cobrada.
     */
    public function cobrar(Reserva $reserva)
    {
        $reserva->estado = 'cobrada';
        $reserva->save();

        return response()->json(['success' => true]);
    }

    public function pending()
    {
        $reservas = Reserva::with('cliente')
            ->where('estado', 'Pendiente')
            ->orderBy('fecha')
            ->paginate(15);

        return view('reservas.pending', compact('reservas'));
    }

    public function confirmPending(Reserva $reserva)
    {
        $oldEstado = $reserva->estado;

        if ($oldEstado === 'Confirmada') {
            return redirect()
                ->back()
                ->with('success', 'Esta reserva ya estaba confirmada.');
        }

        if ($oldEstado === 'Cancelada') {
            return redirect()
                ->back()
                ->with('error', 'No es posible confirmar una reserva cancelada.');
        }

        $reserva->estado = 'Confirmada';
        $reserva->save();

        $this->notifyClientReservationConfirmed($reserva);

        return redirect()
            ->back()
            ->with('success', 'Reserva confirmada y añadida al calendario.');
    }

    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        //
    }

public function store(Request $request)
{
    // 1) Validación, ahora con repeat_enabled y repeat_until
    $data = $request->validate([
        'type'           => 'required',
        'start'          => 'required|date',                // "YYYY-MM-DD HH:MM"
        'duration'       => 'nullable|integer|min:1',
        'entrenador_id'  => 'required_if:type,Clase|nullable',
        'estado'         => 'required|in:Confirmada,Pendiente,Cancelada,No Asistida',
        'cliente_id'     => 'required_if:type,Reserva|integer|exists:clientes,id',
        'servicio_id'    => [
            Rule::requiredIf(in_array($request->input('type'), ['Reserva', 'Clase'])),
            'nullable',
            'integer',
            'exists:items,id',
        ],
    ]);

    // 2) Preparamos la serie de fechas
    $start = Carbon::parse($data['start']);

    $reserva = null;

    DB::transaction(function () use (&$reserva, $data, $start) {
        $orden = null;
        $venta = null;
        $servicio = null;

        if (! empty($data['servicio_id'])) {
            $servicio = Item::findOrFail($data['servicio_id']);

            $orden = OrdenDeCompra::create([
                'fecha_hora'  => $start,
                'responsable' => Auth::id(),
                'cliente'     => $data['cliente_id'] ?? null,
                'activa'      => true,
            ]);

            $venta = new Venta([
                'cuenta'               => $orden->id,
                'producto'             => $servicio->id,
                'cantidad'             => 1,
                'descuento'            => 0,
                'valor_unitario'       => $servicio->valor,
                'valor_total'          => $servicio->valor,
                'porcentaje_comision'  => 0,
                'usuario_id'           => $data['entrenador_id'] ?? Auth::id(),
            ]);
            $venta->valor_total_venta = $servicio->valor;
            $venta->comision = 0;
            $venta->save();
        }

        $reserva = Reserva::create([
            'fecha'         => $start,
            'entrenador_id' => $data['entrenador_id'],
            'estado'        => $data['estado'],
            'duracion'      => $data['duration'] ?? 60,
            'tipo'          => $data['type'],
            'cliente_id'    => $data['cliente_id'] ?? null,
            'responsable_id' => Auth::id(),
            'servicio_id'   => $servicio?->id,
            'orden_id'      => $orden?->id,
            'venta_id'      => $venta?->id,
        ]);
    });

    $peluqueria = Auth::user()->peluqueria; // o where('id', …)

    $alId = $data['cliente_id'] ?? null;

    $al = $alId ? Cliente::find($alId) : null;

    if ($al && $peluqueria) {
        $payload = [
            ucfirst($al->nombres),                     // {{0}} Tipo
            ucfirst($data['type']),                     // {{1}}
            $start->format('d/m/Y H:i'),                // {{2}}
            ($data['duration'] ?? 60).' min',           // {{3}}
            $peluqueria->msj_reserva_confirmada ?? '¡Te esperamos!', // {{4}}
            "https://wa.me/{$peluqueria->telefono}?text=Hola"  // {{5}}
        ];

        if ($al->whatsapp && $peluqueria->msj_reserva_confirmada) {
            $al->notify(new OneMsgTemplateNotification('reserva', array_merge(
                $payload,
                ['nombre'=>$al->nombres]
            )));
        }
    }

    return redirect()
        ->route('reservas.calendar')
        ->with('success', "Reserva creada correctamente.");
}



public function availability(Request $request)
{
    $date = $request->query('date');

    if (! $date) {
        return response()->json(['error' => 'Falta parámetro date'], 422);
    }

    try {
        $workStart = Carbon::parse("$date 05:00");
        $workEnd   = Carbon::parse("$date 22:00");
        $interval  = 30; // minutos

        $userId = Auth::id();

        $reservas = Reserva::query()
            ->where(function ($q) use ($userId) {
                $q->where('entrenador_id', $userId)
                  ->orWhere('cliente_id', $userId);
            })
            ->whereRaw('TRIM(LOWER(estado)) <> ?', ['cancelada'])
            ->whereDate('fecha', $date)
            ->get(['fecha as start', 'duracion as duration'])
            ->map(function ($r) {
                $start = Carbon::parse($r->start);
                $mins  = max(0, (int) $r->duration);
                $end   = (clone $start)->addMinutes($mins); // sin colchón
                return compact('start', 'end');
            });

        $slots  = [];
        $cursor = $workStart->copy();

        while ($cursor->lt($workEnd)) {
            $next = $cursor->copy()->addMinutes($interval);

            // Hacemos el final inclusivo:
            // bloquea el slot si [cursor,next) ∩ [start,end] ≠ ∅
            $ocupado = $reservas->first(function ($r) use ($cursor, $next) {
                return $cursor->lte($r['end']) && $next->gt($r['start']);
            });

            if (! $ocupado) {
                $slots[] = $cursor->format('H:i');
            }
            $cursor->addMinutes($interval);
        }

        return response()->json([
            'slots'   => $slots,
            'minTime' => $workStart->format('H:i:s'),
            'maxTime' => $workEnd->format('H:i:s'),
        ], 200);

    } catch (\Throwable $e) {
        \Log::error("Availability error: {$e->getMessage()}");
        return response()->json(['error' => 'Error interno'], 500);
    }
}



    /**
     * Display the specified resource.
     */
    public function show(Reserva $reserva)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Reserva $reserva)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
   
public function update(Request $request, Reserva $reserva)
{
	
   $oldEstado=$reserva->estado;
   $oldfecha=$reserva->fecha;
        $data = $request->validate([
            'type'          => ['required', 'string', 'max:255'],
            'start'         => 'required|date',
            'duration'      => 'integer|min:1',
            'estado'        => 'required|in:Confirmada,Pendiente,Cancelada,No Asistida',

            'cliente_id'    => 'integer|exists:clientes,id',
            'entrenador_id' => 'integer|nullable',
            'servicio_id'   => [
                Rule::requiredIf(in_array($request->input('type'), ['Reserva', 'Clase'])),
                'nullable',
                'integer',
                'exists:items,id',
            ],

        ]);
    
	 $newEstado = $data['estado'];
	  $peluqueria    = Auth::user()->peluqueria; // o where('id', …)
    
	 
         $start = Carbon::parse($data['start']);
        $alId = in_array($data['type'], ['Reserva', 'Clase']) ? ($data['cliente_id'] ?? $reserva->cliente_id) : null;
        $al = $alId ? Cliente::find($alId) : null;

        if ($al && $peluqueria) {
            $payload = [
                ucfirst($al->nombres),                     // {{0}} Tipo
                ucfirst($data['type']),                     // {{1}}
                $start->format('d/m/Y H:i'),                // {{2}}
                ($data['duration'] ?? 60).' min',           // {{3}}
                $peluqueria->msj_reserva_confirmada ?? '¡Te esperamos!', // {{4}}
                "https://wa.me/{$peluqueria->telefono}?text=Hola"  // {{5}}
            ];

            if ($al->whatsapp) {
                $al->notify(new OneMsgTemplateNotification('cambio_clase', array_merge(
                    $payload,
                    ['nombre'=>$al->nombres]
                )));
            }
        }

        $clienteId = in_array($data['type'], ['Reserva', 'Clase'])
            ? $data['cliente_id']
            : $reserva->cliente_id;

        DB::transaction(function () use ($reserva, $data, $start, $clienteId) {
            $servicio = ! empty($data['servicio_id']) ? Item::findOrFail($data['servicio_id']) : null;

            $orden = $reserva->orden;
            $venta = $reserva->venta;

            if ($servicio) {
                if (! $orden) {
                    $orden = OrdenDeCompra::create([
                        'fecha_hora'  => $start,
                        'responsable' => Auth::id(),
                        'cliente'     => $clienteId,
                        'activa'      => true,
                    ]);
                } else {
                    $orden->fecha_hora  = $start;
                    $orden->responsable = Auth::id();
                    $orden->cliente     = $clienteId;
                    $orden->activa      = true;
                    $orden->save();
                }

                if (! $venta) {
                    $venta = new Venta([
                        'cuenta'               => $orden->id,
                        'producto'             => $servicio->id,
                        'cantidad'             => 1,
                        'descuento'            => 0,
                        'valor_unitario'       => $servicio->valor,
                        'valor_total'          => $servicio->valor,
                        'porcentaje_comision'  => 0,
                        'usuario_id'           => $data['entrenador_id'] ?? Auth::id(),
                    ]);
                } else {
                    $venta->cuenta              = $orden->id;
                    $venta->producto            = $servicio->id;
                    $venta->cantidad            = 1;
                    $venta->descuento           = 0;
                    $venta->valor_unitario      = $servicio->valor;
                    $venta->valor_total         = $servicio->valor;
                    $venta->porcentaje_comision = 0;
                    $venta->usuario_id          = $data['entrenador_id'] ?? $venta->usuario_id ?? Auth::id();
                }

                $venta->valor_total_venta = $servicio->valor;
                $venta->comision = 0;
                $venta->save();

                $reserva->fill([
                    'orden_id'    => $orden->id,
                    'venta_id'    => $venta->id,
                    'servicio_id' => $servicio->id,
                ]);
            } else {
                $reserva->fill([
                    'servicio_id' => null,
                ]);
            }

            $reserva->fill([
                'tipo'          => $data['type'],
                'fecha'         => $data['start'],
                'duracion'      => $data['duration'],
                'estado'        => $data['estado'],
                'cliente_id'    => $clienteId,
                'entrenador_id' => $data['entrenador_id'] ?? $reserva->entrenador_id,
                'responsable_id' => Auth::id(),
            ])->save();
        });

   

    return redirect()
           ->route('reservas.calendar')
           ->with('success', "{$data['type']} actualizada correctamente.");
}


    /**
     * Remove the specified resource from storage.
     */
    public function cancel(Request $request, Reserva $reserva)
    {
        [$wasAlreadyCancelled, $updatedReserva] = $this->cancelReservation($reserva);

        if ($request->expectsJson() || $request->wantsJson()) {
            return response()->json([
                'success' => true,
                'already_cancelled' => $wasAlreadyCancelled,
                'message' => 'Reserva cancelada correctamente.',
                'reserva' => $updatedReserva,
            ]);
        }

        return redirect()
            ->back()
            ->with('success', 'Reserva cancelada correctamente.');
    }

    public function destroy(Request $request, Reserva $reserva)
    {
        [$wasAlreadyCancelled, $updatedReserva] = $this->cancelReservation($reserva);

        if ($request->expectsJson() || $request->wantsJson()) {
            return response()->json([
                'success' => true,
                'already_cancelled' => $wasAlreadyCancelled,
                'message' => 'Reserva cancelada correctamente.',
                'reserva' => $updatedReserva,
            ]);
        }

        return redirect()
            ->route('reservas.horario')
            ->with('success', 'Reserva cancelada correctamente.');
    }

    private function notifyClientReservationConfirmed(Reserva $reserva): void
    {
        $user = Auth::user();
        $peluqueria = $user?->peluqueria;

        if (! $peluqueria) {
            return;
        }

        $reserva->loadMissing('cliente');
        $cliente = $reserva->cliente;

        if (! $cliente || ! $cliente->whatsapp) {
            return;
        }

        $mensaje = trim((string) ($peluqueria->msj_reserva_confirmada ?? ''));

        if ($mensaje === '') {
            return;
        }

        $telefono = trim((string) ($peluqueria->telefono ?? ''));

        if ($telefono === '') {
            return;
        }

        try {
            $fecha = Carbon::parse($reserva->fecha);
        } catch (Throwable $exception) {
            Log::warning('No se pudo parsear la fecha de la reserva para la notificación de confirmación.', [
                'reserva_id' => $reserva->id,
                'fecha' => $reserva->fecha,
                'exception' => $exception->getMessage(),
            ]);

            return;
        }

        $payload = [
            ucfirst((string) $cliente->nombres),
            ucfirst((string) ($reserva->tipo ?? 'Reserva')),
            $fecha->format('d/m/Y H:i'),
            ($reserva->duracion ?? 60) . ' min',
            $mensaje,
            "https://wa.me/{$telefono}?text=Hola",
        ];

        try {
            $cliente->notify(new OneMsgTemplateNotification('reserva', array_merge(
                $payload,
                ['nombre' => $cliente->nombres]
            )));
        } catch (Throwable $exception) {
            Log::warning('No se pudo enviar la confirmación por WhatsApp al cliente.', [
                'reserva_id' => $reserva->id,
                'cliente_id' => $cliente->id,
                'exception' => $exception->getMessage(),
            ]);
        }
    }

    protected function cancelReservation(Reserva $reserva): array
    {
        $currentEstado = trim((string) $reserva->estado);
        $wasAlreadyCancelled = strcasecmp($currentEstado, 'Cancelada') === 0;

        if (! $wasAlreadyCancelled) {
            $reserva->forceFill(['estado' => 'Cancelada'])->save();

            $cliente    = $reserva->cliente;
            $templateId = config('services.onemsg.templates.cancelacion');

            if ($cliente && $cliente->whatsapp && $templateId) {
                $payload = [
                    0 => ucfirst($cliente->nombres ?? ''),
                    1 => ucfirst($reserva->tipo ?? 'Reserva'),
                    2 => Carbon::parse($reserva->fecha)->format('d/m/Y H:i'),
                ];

                $cliente->notify(new OneMsgTemplateNotification('cancelacion', array_merge(
                    $payload,
                    ['nombre' => $cliente->nombres ?? '']
                )));
            }
        }

        return [$wasAlreadyCancelled, $reserva->fresh()];
    }
}

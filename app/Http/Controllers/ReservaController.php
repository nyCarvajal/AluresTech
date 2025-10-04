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
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use Carbon\Carbon; 
use Illuminate\Support\Facades\Validator;
use App\Notifications\OneMsgTemplateNotification;
use Illuminate\Support\Facades\Log;
use App\Models\MembresiaCliente;


class ReservaController extends Controller
{
	
        public function calendar()
{
    $entrenadores = User::all(); // o tu filtro de usuarios con rol “entrenador”
        
    $tipocitas    = Tipocita::all();
    return view('reservas.calendar', compact('entrenadores', 'tipocitas'));


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

        return view('reservas.horario', compact(
            'canchas', 'timeslots', 'events', 'prevDate', 'nextDate', 'entrenadores'
        ));
    }




	public function events(Request $request)
{
    // 1) Trae y filtra la consulta (mejor en base de datos, no en colección)
    $query = Reserva::with(['cliente', 'entrenador'])
        ->where('estado', 'Confirmada');
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
        }

        $base = [
            'id'              => $r->id,
            'start'           => $start,
            'end'             => $end,
            'type'            => $r->tipo,
            'status'          => $r->estado,
            'duration'        => $r->duracion,
            'title'           => optional($r->cliente)->nombres . ' ' . optional($r->cliente)->apellidos,
            'borderColor'     => $color,
            'textColor'       => $textColor,
            'backgroundColor' => $color,
            'extendedProps'   => [
                'tipo'          => $r->tipo,              // reserva | torneo | clase
                'estado'        => $r->estado,            // confirmada | pendiente
                'entrenador_id' => $r->entrenador_id,
                'cliente_id'    => $r->cliente_id,
            ],
            'cancha_id'       => $r->cancha_id,
            'entrenador_id'   => $r->entrenador_id,
            'cliente_id'      => $r->cliente_id,
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

        $tipoReserva = $reserva->type ?? 'Reserva';
        if ($oldEstado !== 'Confirmada' && in_array($tipoReserva, ['Reserva', 'Clase'])) {
            $clienteId = $reserva->cliente_id;
            $membresia = MembresiaCliente::where('cliente_id', $clienteId)
                ->where('estado', 1)
                ->latest()
                ->first();

            if ($membresia) {
                $campo = $tipoReserva === 'Clase' ? 'clasesVistas' : 'numReservas';
                $membresia->increment($campo);
            }
        }

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
      
        
    ]);

    // 2) Preparamos la serie de fechas
    $start = Carbon::parse($data['start']);  
 

   
                $res = Reserva::create([
                    'fecha'         => $start,
                    'entrenador_id' => $data['entrenador_id'],
                    'estado'        => $data['estado'],
                    'duracion'      => $data['duration'] ?? 60,
                    'tipo'          => $data['type'],
                    'cliente_id'    => $data['cliente_id'],
                    
                ]);
              

          
		  $peluqueria    = Auth::user()->peluqueria; // o where('id', …)
    

            
        $alId = $data['cliente_id'];

                    $al = Cliente::find($alId);
                     $payload = [
            ucfirst($al->nombres),                     // {{0}} Tipo
            ucfirst($data['type']),                     // {{1}}
            $start->format('d/m/Y H:i'),                   // {{2}}
            ($data['duration'] ?? 60).' min',           // {{3}}
            $peluqueria->msj_reserva_confirmada ?? '¡Te esperamos!', // {{4}}
            "https://wa.me/{$peluqueria->telefono}?text=Hola"  // {{5}}
        ];

        $al = Cliente::find($alId);

        if ($al && $al->whatsapp && $peluqueria->msj_reserva_confirmada) {

            $al->notify(new OneMsgTemplateNotification('reserva', array_merge(
                $payload,
                ['nombre'=>$al->nombres]
            )));
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
            'type'          => ['required', Rule::in(['Reserva','Clase','Torneo'])],
            'start'         => 'required|date',
            'duration'      => 'integer|min:1',
            'estado'        => 'required|in:Confirmada,Pendiente,Cancelada,No Asistida',
            'cancha_id'     => 'required_if:type,Reserva,Clase|exists:canchas,id',
            'cliente_id'    => 'required_if:type,Reserva,Clase|exists:clientes,id',
            'entrenador_id' => 'required_if:type,Clase|nullable',
            'responsable_id'=> 'required_if:type,Torneo|exists:clientes,id',
            'canchas'       => 'required_if:type,Torneo|array',
            'canchas.*'     => 'exists:canchas,id',
        ]);
    
	 $newEstado = $data['estado'];
	  $peluqueria    = Auth::user()->peluqueria; // o where('id', …)
    
	 
         $start = Carbon::parse($data['start']);
         $alId = $data['cliente_id'];
         $al = Cliente::find($alId);
         $payload = [
            ucfirst($al->nombres),                     // {{0}} Tipo
            ucfirst($data['type']),                     // {{1}}
            $start->format('d/m/Y H:i'),                   // {{2}}
            ($data['duration'] ?? 60).' min',           // {{3}}
            $peluqueria->msj_reserva_confirmada ?? '¡Te esperamos!', // {{4}}
            "https://wa.me/{$peluqueria->telefono}?text=Hola"  // {{5}}
        ];

        if ($al && $al->whatsapp) {
            $al->notify(new OneMsgTemplateNotification('cambio_clase', array_merge(
                $payload,
                ['nombre'=>$al->nombres]
            )));
        }
	 
	
 
if ($oldEstado !== $newEstado && in_array($data['type'], ['Reserva', 'Clase'])) {
    $clienteId = $data['cliente_id'];
    $memb = MembresiaCliente::where('cliente_id', $clienteId)
            ->where('estado', 1)
            ->latest()
            ->first();

    $campo = $data['type'] === 'Clase' ? 'clasesVistas' : 'numReservas';

    if ($newEstado === 'Cancelada') {
        if ($memb) {
            $memb->decrement($campo);
        }

    }
    elseif ($newEstado === 'Confirmada') {
        if ($memb) {
            $memb->increment($campo);
        }
        Log::info("Reserva #{$reserva->id} CONFIRMADA: -" .
                  ($memb ? "1 en {$campo}" : "(sin membresía)") .
                  " para cliente {$clienteId}.");
    }
}

// 2) Actualizar los campos de la reserva
    $reserva->fill([
        'tipo'          => $data['type'],
        'fecha'         => $data['start'],
        'duracion'      => $data['duration'] ?? $reserva->duration,
        'estado'        => $data['estado'],
        'cancha_id'     => $data['cancha_id'] ?? null,
        'responsable_id'=> $data['responsable_id'] ?? $reserva->responsable_id,
        'cliente_id'    => in_array($data['type'], ['Reserva','Clase']) ? $data['cliente_id'] : $reserva->cliente_id,
        'entrenador_id' => $data['entrenador_id'] ?? $reserva->entrenador_id,
    ])->save();

    if ($data['type'] === 'Torneo') {
        $reserva->canchas()->sync($data['canchas']);
    }

    return redirect()
           ->route('reservas.calendar')
           ->with('success', "{$data['type']} actualizada correctamente.");
}


    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Reserva $reserva)
    {
		 $reserva->estado = 'Cancelada';

    // 3. Guardas los cambios en la BD
    $reserva->save();
		
         return redirect()
           ->route('reservas.horario')
           ->with('success', "Reserva cancelada correctamente.");
    }
}

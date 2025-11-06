<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Cliente;
use App\Models\OrdenDeCompra;
use App\Models\Pago;
use App\Models\Reserva;
use App\Models\Venta;
use Carbon\Carbon;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Auth;

class DashboardController extends Controller
{
    public function index()
    {
        if (! Auth::check()) {
            return redirect()->route('auth.signin');
        }

        $now = now();
        $today = $now->copy()->startOfDay();
        $endOfDay = $now->copy()->endOfDay();
        $windowEnd = $now->copy()->addHours(5);
        $windowEnd = $windowEnd->greaterThan($endOfDay)
            ? $endOfDay
            : $windowEnd;

        $inicioMes = $now->copy()->startOfMonth();
        $finMes = $now->copy()->endOfMonth();

        $totalClientes = Cliente::count();

        $totalPagosMes = Pago::whereBetween('fecha_hora', [$inicioMes, $finMes])
            ->whereHas('ordenDeCompra', function ($query) {
                $query->where('activa', 1);
            })
            ->sum('valor');

        $pagosHoy = Pago::whereBetween('fecha_hora', [$today, $endOfDay])
            ->whereHas('ordenDeCompra', function ($query) {
                $query->where('activa', 1);
            })
            ->sum('valor');

        $ayerInicio = $today->copy()->subDay();
        $ayerFin = $ayerInicio->copy()->endOfDay();

        $pagosAyer = Pago::whereBetween('fecha_hora', [$ayerInicio, $ayerFin])
            ->whereHas('ordenDeCompra', function ($query) {
                $query->where('activa', 1);
            })
            ->sum('valor');

        $variacionCaja = $pagosHoy - $pagosAyer;

        $ordenesHoy = OrdenDeCompra::with(['ventas', 'pagos'])
            ->where('activa', 1)
            ->whereBetween('fecha_hora', [$today, $endOfDay])
            ->get();

        $ingresosPendientesHoy = (int) $ordenesHoy->sum(function ($orden) {
            $totalVentas = (int) $orden->ventas->sum('valor_total');
            $totalPagos = (int) $orden->pagos->sum('valor');

            return max($totalVentas - $totalPagos, 0);
        });

        $reservasHoy = Reserva::with('entrenador')
            ->whereBetween('fecha', [$today, $endOfDay])
            ->get();

        $confirmadasHoy = $reservasHoy->where('estado', 'Confirmada')->count();
        $pendientesHoy = $reservasHoy->where('estado', 'Pendiente')->count();
        $canceladasHoy = $reservasHoy->where('estado', 'Cancelada')->count();
        $noAsistidasHoy = $reservasHoy->where('estado', 'No Asistida')->count();
        $totalAgendadasHoy = $reservasHoy->count();

        $baseAsistencia = $confirmadasHoy + $pendientesHoy + $canceladasHoy;
        $asistenciaPorcentaje = $baseAsistencia > 0
            ? round(($confirmadasHoy / max($baseAsistencia, 1)) * 100)
            : 100;

        $ausenciasRecuperadas = Reserva::whereDate('fecha', $today)
            ->whereDate('updated_at', $today)
            ->whereColumn('updated_at', '>', 'created_at')
            ->where('estado', 'Confirmada')
            ->count();

        $totalReservas = Reserva::whereBetween('fecha', [$inicioMes, $finMes])
            ->where('estado', '!=', 'Cancelada')
            ->count();

        $totalClases = Reserva::where('tipo', 'Clase')
            ->where('estado', '!=', 'Cancelada')
            ->whereBetween('fecha', [$inicioMes, $finMes])
            ->count();

        $clientes = Cliente::latest()->take(10)->get();

        $cuentas = OrdenDeCompra::with('clienterel')
            ->whereBetween('fecha_hora', [$inicioMes, $finMes])
            ->whereNotNull('cliente')
            ->where('activa', 1)
            ->whereHas('clienterel')
            ->withSum('ventas as monto', 'valor_total')
            ->latest('fecha_hora')
            ->take(10)
            ->get(['id', 'fecha_hora']);

        $teamPerformance = Venta::selectRaw('usuario_id, SUM(valor_total) as total_cobrado, COUNT(*) as servicios')
            ->whereBetween('created_at', [$today, $endOfDay])
            ->whereNotNull('usuario_id')
            ->groupBy('usuario_id')
            ->with('barbero')
            ->orderByDesc('total_cobrado')
            ->get();

        $totalEquipoHoy = (int) $teamPerformance->sum('total_cobrado');

        $huecos = $this->calcularHuecosDisponibles($reservasHoy, $now, $windowEnd);
        $huecosDestacados = $huecos->take(3);
        $totalHuecosDisponibles = $huecos->count();

        $fechaHoyLegible = $now->locale('es')
            ->translatedFormat('l d \d\e F');

        return view('admin.index', [
            'totalReservas' => $totalReservas,
            'totalClases' => $totalClases,
            'totalClientes' => $totalClientes,
            'totalPagosMes' => $totalPagosMes,
            'clientes' => $clientes,
            'cuentas' => $cuentas,
            'pagosHoy' => $pagosHoy,
            'ingresosPendientesHoy' => $ingresosPendientesHoy,
            'asistenciaPorcentaje' => $asistenciaPorcentaje,
            'ausenciasRecuperadas' => $ausenciasRecuperadas,
            'ausenciasHoy' => $noAsistidasHoy,
            'totalAgendadasHoy' => $totalAgendadasHoy,
            'confirmadasHoy' => $confirmadasHoy,
            'totalHuecosDisponibles' => $totalHuecosDisponibles,
            'huecosDestacados' => $huecosDestacados,
            'teamPerformance' => $teamPerformance,
            'totalEquipoHoy' => $totalEquipoHoy,
            'variacionCaja' => $variacionCaja,
            'fechaHoyLegible' => $fechaHoyLegible,
        ]);
    }

    private function calcularHuecosDisponibles(Collection $reservasHoy, Carbon $windowStart, Carbon $windowEnd): Collection
    {
        $slots = collect();

        $reservasOrdenadas = $reservasHoy
            ->filter(fn ($reserva) => ! empty($reserva->fecha))
            ->sortBy('fecha')
            ->values();

        $cursor = $windowStart->copy();

        foreach ($reservasOrdenadas as $reserva) {
            $inicio = Carbon::parse($reserva->fecha);
            $duracion = (int) ($reserva->duracion ?? 30);
            if ($duracion <= 0) {
                $duracion = 30;
            }
            $fin = $inicio->copy()->addMinutes($duracion);

            if ($fin->lessThanOrEqualTo($windowStart)) {
                continue;
            }

            if ($inicio->greaterThanOrEqualTo($windowEnd)) {
                break;
            }

            if ($inicio->greaterThan($cursor)) {
                $inicioHueco = $cursor->copy();
                $finHueco = $inicio->copy()->min($windowEnd);
                $duracionHueco = $inicioHueco->diffInMinutes($finHueco);

                if ($duracionHueco >= 30) {
                    $slots->push((object) [
                        'inicio' => $inicioHueco,
                        'duracion' => min($duracionHueco, 60),
                        'barbero' => $this->obtenerNombreBarbero($reserva->entrenador),
                        'servicio' => $this->sugerirServicioPorDuracion($duracionHueco),
                    ]);
                }
            }

            if ($fin->greaterThan($cursor)) {
                $cursor = $fin->copy();
            }

            if ($cursor->greaterThanOrEqualTo($windowEnd)) {
                break;
            }
        }

        if ($cursor->lessThan($windowEnd)) {
            $duracionHueco = $cursor->diffInMinutes($windowEnd);

            if ($duracionHueco >= 30) {
                $ultimoBarbero = optional($reservasOrdenadas->last())->entrenador;

                $slots->push((object) [
                    'inicio' => $cursor->copy(),
                    'duracion' => min($duracionHueco, 60),
                    'barbero' => $this->obtenerNombreBarbero($ultimoBarbero),
                    'servicio' => $this->sugerirServicioPorDuracion($duracionHueco),
                ]);
            }
        }

        if ($slots->isEmpty()) {
            $duracionHueco = $windowStart->diffInMinutes($windowEnd);

            if ($duracionHueco >= 30) {
                $slots->push((object) [
                    'inicio' => $windowStart->copy(),
                    'duracion' => min($duracionHueco, 60),
                    'barbero' => 'Equipo',
                    'servicio' => $this->sugerirServicioPorDuracion($duracionHueco),
                ]);
            }
        }

        return $slots->sortBy('inicio')->values();
    }

    private function obtenerNombreBarbero($barbero = null): string
    {
        if (! $barbero) {
            return 'Equipo';
        }

        $nombre = $barbero->nombre_completo
            ?? $barbero->nombre
            ?? $barbero->nombres
            ?? null;

        return $nombre ? trim($nombre) : 'Equipo';
    }

    private function sugerirServicioPorDuracion(int $minutos): string
    {
        if ($minutos <= 35) {
            return 'Corte rápido';
        }

        if ($minutos <= 55) {
            return 'Corte + Barba';
        }

        return 'Color / Tinte';
    }
}

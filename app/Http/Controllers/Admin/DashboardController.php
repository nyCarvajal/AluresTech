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

        $today = Carbon::today();
        $now = Carbon::now();
        $windowEnd = $now->copy()->addHours(5);
        $windowEnd = $windowEnd->greaterThan($today->copy()->endOfDay())
            ? $today->copy()->endOfDay()
            : $windowEnd;

        $totalClientes = Cliente::count();

        $totalPagosMes = Pago::whereYear('fecha_hora', $today->year)
            ->whereMonth('fecha_hora', $today->month)
            ->whereHas('ordenDeCompra', function ($query) {
                $query->where('activa', 1);
            })
            ->sum('valor');

        $pagosHoy = Pago::whereDate('fecha_hora', $today)
            ->whereHas('ordenDeCompra', function ($query) {
                $query->where('activa', 1);
            })
            ->sum('valor');

        $pagosAyer = Pago::whereDate('fecha_hora', $today->copy()->subDay())
            ->whereHas('ordenDeCompra', function ($query) {
                $query->where('activa', 1);
            })
            ->sum('valor');

        $variacionCaja = $pagosHoy - $pagosAyer;

        $ordenesHoy = OrdenDeCompra::with(['ventas', 'pagos'])
            ->where('activa', 1)
            ->whereDate('fecha_hora', $today)
            ->get();

        $ingresosPendientesHoy = (int) $ordenesHoy->sum(function ($orden) {
            $totalVentas = (int) $orden->ventas->sum('valor_total');
            $totalPagos = (int) $orden->pagos->sum('valor');

            return max($totalVentas - $totalPagos, 0);
        });

        $reservasHoy = Reserva::with('entrenador')
            ->whereDate('fecha', $today)
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

        $totalReservas = Reserva::where('tipo', 'Reserva')
            ->where('estado', '!=', 'Cancelada')
            ->whereYear('fecha', $today->year)
            ->whereMonth('fecha', $today->month)
            ->count();

        $totalClases = Reserva::where('tipo', 'Clase')
            ->where('estado', '!=', 'Cancelada')
            ->whereYear('fecha', $today->year)
            ->whereMonth('fecha', $today->month)
            ->count();

        $clientes = Cliente::latest()->take(10)->get();

        $cuentas = OrdenDeCompra::with('clienterel')
            ->whereYear('fecha_hora', $today->year)
            ->whereMonth('fecha_hora', $today->month)
            ->whereNotNull('cliente')
            ->where('activa', 1)
            ->whereHas('clienterel')
            ->withSum('ventas as monto', 'valor_total')
            ->latest('fecha_hora')
            ->take(10)
            ->get(['id', 'fecha_hora']);

        $teamPerformance = Venta::selectRaw('usuario_id, SUM(valor_total) as total_cobrado, COUNT(*) as servicios')
            ->whereDate('created_at', $today)
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

        $reservasFiltradas = $reservasHoy->filter(function ($reserva) use ($windowEnd) {
            if (! $reserva->fecha) {
                return false;
            }

            $inicio = Carbon::parse($reserva->fecha);

            return $inicio->lessThan($windowEnd);
        });

        $reservasAgrupadas = $reservasFiltradas->groupBy('entrenador_id');

        foreach ($reservasAgrupadas as $reservasBarbero) {
            $reservasOrdenadas = $reservasBarbero->sortBy('fecha')->values();

            $cursor = $windowStart->copy();

            foreach ($reservasOrdenadas as $reserva) {
                $inicio = Carbon::parse($reserva->fecha);
                $duracion = (int) ($reserva->duracion ?? 30);
                if ($duracion <= 0) {
                    $duracion = 30;
                }
                $fin = $inicio->copy()->addMinutes($duracion);

                if ($fin->lessThanOrEqualTo($windowStart)) {
                    $cursor = $cursor->max($fin);
                    continue;
                }

                if ($inicio->greaterThan($cursor)) {
                    $inicioHueco = $cursor->copy();
                    $finHueco = $inicio->copy();

                    if ($inicioHueco->lessThan($windowEnd)) {
                        $finHueco = $finHueco->min($windowEnd);
                        $duracionHueco = $inicioHueco->diffInMinutes($finHueco);

                        if ($duracionHueco >= 30) {
                            $slots->push((object) [
                                'inicio' => $inicioHueco->copy(),
                                'duracion' => min($duracionHueco, 60),
                                'barbero' => optional($reserva->entrenador)->nombre ?? 'Equipo',
                                'servicio' => $this->sugerirServicioPorDuracion($duracionHueco),
                            ]);
                        }
                    }
                }

                $cursor = $cursor->max($fin);

                if ($cursor->greaterThanOrEqualTo($windowEnd)) {
                    break;
                }
            }

            if ($cursor->lessThan($windowEnd)) {
                $inicioHueco = $cursor->copy();
                $finHueco = $windowEnd->copy();
                $duracionHueco = $inicioHueco->diffInMinutes($finHueco);

                if ($duracionHueco >= 30) {
                    $slots->push((object) [
                        'inicio' => $inicioHueco,
                        'duracion' => min($duracionHueco, 60),
                        'barbero' => optional($reservasOrdenadas->last()->entrenador)->nombre ?? 'Equipo',
                        'servicio' => $this->sugerirServicioPorDuracion($duracionHueco),
                    ]);
                }
            }
        }

        return $slots->sortByDesc('duracion')->values();
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

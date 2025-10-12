<?php

namespace App\Http\Controllers;

use App\Models\Banco;
use App\Models\Cliente;
use App\Models\Item;
use App\Models\Pago;
use App\Models\Proveedor;
use App\Models\Salida;
use App\Models\User;
use App\Models\Venta;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Pagination\LengthAwarePaginator;

class AdministrativeReportController extends Controller
{
    public function index(Request $request)
    {
        $activeTab = $request->input('tab', 'ventas');

        $ventasPaginator = $this->emptyPaginator($request, 'ventas_page');
        $comisionesPaginator = $this->emptyPaginator($request, 'comision_page');
        $gastosPaginator = $this->emptyPaginator($request, 'gasto_page');
        $ingresosPaginator = $this->emptyPaginator($request, 'ingreso_page');

        $ventasTotal = 0;
        $comisionesTotal = 0;
        $gastosTotal = 0;
        $ingresosTotal = 0;

        $ventasFilters = [
            'desde' => $request->input('ventas_desde'),
            'hasta' => $request->input('ventas_hasta'),
            'item' => $request->input('ventas_item'),
        ];

        $comisionFilters = [
            'desde' => $request->input('comision_desde'),
            'hasta' => $request->input('comision_hasta'),
            'empleado' => $request->input('comision_empleado'),
            'cliente' => $request->input('comision_cliente'),
        ];

        $gastoFilters = [
            'desde' => $request->input('gasto_desde'),
            'hasta' => $request->input('gasto_hasta'),
            'proveedor' => $request->input('gasto_proveedor'),
            'responsable' => $request->input('gasto_responsable'),
            'concepto' => $request->input('gasto_concepto'),
        ];

        $ingresoFilters = [
            'desde' => $request->input('ingreso_desde'),
            'hasta' => $request->input('ingreso_hasta'),
            'banco' => $request->input('ingreso_banco'),
            'cliente' => $request->input('ingreso_cliente'),
        ];

        $chartLabels = [];
        $chartIngresos = [];
        $chartGastos = [];
        $chartError = null;

        $items = collect();
        $clientes = collect();
        $empleados = collect();
        $proveedores = collect();
        $bancos = collect();

        $pageError = null;

        try {
            $currentMonthStart = Carbon::now()->startOfMonth();
            $currentMonthEnd = Carbon::now()->endOfMonth();

            // === Ventas ===
            $ventasDesde = $ventasFilters['desde'];
            $ventasHasta = $ventasFilters['hasta'];
            $ventasItem = $ventasFilters['item'];

            if (!$ventasDesde && !$ventasHasta) {
                $ventasDesde = $currentMonthStart->toDateString();
                $ventasHasta = $currentMonthEnd->toDateString();
            }

            $ventasQuery = Venta::with(['item', 'orden.clienterel'])
                ->when($ventasDesde, function ($query) use ($ventasDesde) {
                    return $query->whereDate('created_at', '>=', Carbon::parse($ventasDesde));
                })
                ->when($ventasHasta, function ($query) use ($ventasHasta) {
                    return $query->whereDate('created_at', '<=', Carbon::parse($ventasHasta));
                })
                ->when($ventasItem, function ($query) use ($ventasItem) {
                    return $query->where('producto', $ventasItem);
                });

            $ventasTotal = (clone $ventasQuery)->sum('valor_total');
            $ventasPaginator = $ventasQuery
                ->orderByDesc('created_at')
                ->paginate(10, ['*'], 'ventas_page')
                ->appends($request->except('ventas_page'));

            // === Comisiones ===
            $comisionDesde = $comisionFilters['desde'];
            $comisionHasta = $comisionFilters['hasta'];
            $comisionEmpleado = $comisionFilters['empleado'];
            $comisionCliente = $comisionFilters['cliente'];

            $comisionesQuery = Pago::with(['ordenDeCompra.clienterel', 'responsableUsuario'])
                ->whereNotNull('responsable')
                ->when($comisionDesde, function ($query) use ($comisionDesde) {
                    return $query->whereDate('fecha_hora', '>=', Carbon::parse($comisionDesde));
                })
                ->when($comisionHasta, function ($query) use ($comisionHasta) {
                    return $query->whereDate('fecha_hora', '<=', Carbon::parse($comisionHasta));
                })
                ->when($comisionEmpleado, function ($query) use ($comisionEmpleado) {
                    return $query->where('responsable', $comisionEmpleado);
                })
                ->when($comisionCliente, function ($query) use ($comisionCliente) {
                    $query->whereHas('ordenDeCompra', function ($subQuery) use ($comisionCliente) {
                        $subQuery->where('cliente', $comisionCliente);
                    });
                });

            $comisionesTotal = (clone $comisionesQuery)->sum('valor');
            $comisionesPaginator = $comisionesQuery
                ->orderByDesc('fecha_hora')
                ->paginate(10, ['*'], 'comision_page')
                ->appends($request->except('comision_page'));

            // === Gastos ===
            $gastoDesde = $gastoFilters['desde'];
            $gastoHasta = $gastoFilters['hasta'];
            $gastoProveedor = $gastoFilters['proveedor'];
            $gastoResponsable = $gastoFilters['responsable'];
            $gastoConcepto = $gastoFilters['concepto'];

            $gastosQuery = Salida::with(['tercero', 'responsable'])
                ->when($gastoDesde, function ($query) use ($gastoDesde) {
                    return $query->whereDate('fecha', '>=', Carbon::parse($gastoDesde));
                })
                ->when($gastoHasta, function ($query) use ($gastoHasta) {
                    return $query->whereDate('fecha', '<=', Carbon::parse($gastoHasta));
                })
                ->when($gastoProveedor, function ($query) use ($gastoProveedor) {
                    return $query->where('tercero_id', $gastoProveedor);
                })
                ->when($gastoResponsable, function ($query) use ($gastoResponsable) {
                    return $query->where('responsable_id', $gastoResponsable);
                })
                ->when($gastoConcepto, function ($query) use ($gastoConcepto) {
                    $query->where('concepto', 'like', '%' . $gastoConcepto . '%');
                });

            $gastosTotal = (clone $gastosQuery)->sum('valor');
            $gastosPaginator = $gastosQuery
                ->orderByDesc('fecha')
                ->paginate(10, ['*'], 'gasto_page')
                ->appends($request->except('gasto_page'));

            // === Ingresos ===
            $ingresoDesde = $ingresoFilters['desde'];
            $ingresoHasta = $ingresoFilters['hasta'];
            $ingresoBanco = $ingresoFilters['banco'];
            $ingresoCliente = $ingresoFilters['cliente'];

            $ingresosQuery = Pago::with(['ordenDeCompra.clienterel', 'bancoModel'])
                ->when($ingresoDesde, function ($query) use ($ingresoDesde) {
                    return $query->whereDate('fecha_hora', '>=', Carbon::parse($ingresoDesde));
                })
                ->when($ingresoHasta, function ($query) use ($ingresoHasta) {
                    return $query->whereDate('fecha_hora', '<=', Carbon::parse($ingresoHasta));
                })
                ->when($ingresoBanco, function ($query) use ($ingresoBanco) {
                    return $query->where('banco', $ingresoBanco);
                })
                ->when($ingresoCliente, function ($query) use ($ingresoCliente) {
                    $query->whereHas('ordenDeCompra', function ($subQuery) use ($ingresoCliente) {
                        $subQuery->where('cliente', $ingresoCliente);
                    });
                });

            $ingresosTotal = (clone $ingresosQuery)->sum('valor');
            $ingresosPaginator = $ingresosQuery
                ->orderByDesc('fecha_hora')
                ->paginate(10, ['*'], 'ingreso_page')
                ->appends($request->except('ingreso_page'));

            $ventasFilters['desde'] = $ventasDesde;
            $ventasFilters['hasta'] = $ventasHasta;

            // === Chart: Ingresos vs Gastos ===
            $chartStart = Carbon::now()->subMonths(11)->startOfMonth();
            $chartEnd = Carbon::now()->endOfMonth();

            try {
                $ingresosPorMes = Pago::query()
                    ->select(['fecha_hora', 'valor'])
                    ->whereBetween('fecha_hora', [$chartStart, $chartEnd])
                    ->get()
                    ->groupBy(function (Pago $pago) {
                        $rawFecha = $pago->getRawOriginal('fecha_hora');

                        if (empty($rawFecha) || $rawFecha === '0000-00-00 00:00:00') {
                            return null;
                        }

                        try {
                            return Carbon::parse($rawFecha)->format('Y-m');
                        } catch (\Throwable $exception) {
                            return null;
                        }
                    })
                    ->filter(function ($pagos, $periodo) {
                        return filled($periodo);
                    })
                    ->mapWithKeys(function ($pagos, $periodo) {
                        return [$periodo => $pagos->sum('valor')];
                    });

                $gastosPorMes = Salida::query()
                    ->select(['fecha', 'valor'])
                    ->whereBetween('fecha', [$chartStart, $chartEnd])
                    ->get()
                    ->groupBy(function (Salida $salida) {
                        $rawFecha = $salida->getRawOriginal('fecha');

                        if (empty($rawFecha) || $rawFecha === '0000-00-00') {
                            return null;
                        }

                        try {
                            return Carbon::parse($rawFecha)->format('Y-m');
                        } catch (\Throwable $exception) {
                            return null;
                        }
                    })
                    ->filter(function ($gastos, $periodo) {
                        return filled($periodo);
                    })
                    ->mapWithKeys(function ($gastos, $periodo) {
                        return [$periodo => $gastos->sum('valor')];
                    });

                $spanishMonthNames = [
                    1 => 'Ene',
                    2 => 'Feb',
                    3 => 'Mar',
                    4 => 'Abr',
                    5 => 'May',
                    6 => 'Jun',
                    7 => 'Jul',
                    8 => 'Ago',
                    9 => 'Sep',
                    10 => 'Oct',
                    11 => 'Nov',
                    12 => 'Dic',
                ];

                $cursor = $chartStart->copy();
                while ($cursor <= $chartEnd) {
                    $key = $cursor->format('Y-m');
                    $monthNumber = (int) $cursor->format('n');
                    $monthName = $spanishMonthNames[$monthNumber] ?? $cursor->format('M');
                    $chartLabels[] = $monthName . ' ' . $cursor->format('Y');
                    $chartIngresos[] = (float) ($ingresosPorMes[$key] ?? 0);
                    $chartGastos[] = (float) ($gastosPorMes[$key] ?? 0);
                    $cursor->addMonth();
                }
            } catch (\Throwable $exception) {
                report($exception);

                $chartError = 'No se pudo generar la gráfica de ingresos y gastos por datos con fechas inválidas. Revisa que los pagos y gastos tengan una fecha válida.';
            }

            // Supporting data for filters
            $items = Item::orderBy('nombre')->get();
            $clientes = Cliente::orderBy('nombres')->get();
            $empleados = User::query()
                ->when(optional(Auth::user())->peluqueria_id, function ($query, $peluqueriaId) {
                    $query->where('peluqueria_id', $peluqueriaId);
                })
                ->orderBy('nombre')
                ->get();
            $proveedores = Proveedor::orderBy('nombre')->get();
            $bancos = Banco::orderBy('nombre')->get();
        } catch (\Throwable $exception) {
            report($exception);

            $pageError = 'No se pudo cargar el informe administrativo. Revisa los registros para más detalles.';
        }

        return view('pages.charts', [
            'activeTab' => $activeTab,
            'ventas' => $ventasPaginator,
            'ventasTotal' => $ventasTotal,
            'comisiones' => $comisionesPaginator,
            'comisionesTotal' => $comisionesTotal,
            'gastos' => $gastosPaginator,
            'gastosTotal' => $gastosTotal,
            'ingresos' => $ingresosPaginator,
            'ingresosTotal' => $ingresosTotal,
            'ventasFilters' => $ventasFilters,
            'comisionFilters' => $comisionFilters,
            'gastoFilters' => $gastoFilters,
            'ingresoFilters' => $ingresoFilters,
            'items' => $items,
            'clientes' => $clientes,
            'empleados' => $empleados,
            'proveedores' => $proveedores,
            'bancos' => $bancos,
            'chartData' => [
                'labels' => $chartLabels,
                'ingresos' => $chartIngresos,
                'gastos' => $chartGastos,
            ],
            'chartError' => $chartError,
            'pageError' => $pageError,
        ]);
    }

    protected function emptyPaginator(Request $request, string $pageName): LengthAwarePaginator
    {
        return new LengthAwarePaginator([], 0, 10, 1, [
            'path' => $request->url(),
            'pageName' => $pageName,
        ]);
    }
}

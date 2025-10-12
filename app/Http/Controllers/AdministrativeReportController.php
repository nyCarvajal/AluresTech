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

class AdministrativeReportController extends Controller
{
    public function index(Request $request)
    {
        $activeTab = $request->input('tab', 'ventas');

        $currentMonthStart = Carbon::now()->startOfMonth();
        $currentMonthEnd = Carbon::now()->endOfMonth();

        // === Ventas ===
        $ventasDesde = $request->input('ventas_desde');
        $ventasHasta = $request->input('ventas_hasta');
        $ventasItem = $request->input('ventas_item');

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
        $comisionDesde = $request->input('comision_desde');
        $comisionHasta = $request->input('comision_hasta');
        $comisionEmpleado = $request->input('comision_empleado');
        $comisionCliente = $request->input('comision_cliente');

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
        $gastoDesde = $request->input('gasto_desde');
        $gastoHasta = $request->input('gasto_hasta');
        $gastoProveedor = $request->input('gasto_proveedor');
        $gastoResponsable = $request->input('gasto_responsable');
        $gastoConcepto = $request->input('gasto_concepto');

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
        $ingresoDesde = $request->input('ingreso_desde');
        $ingresoHasta = $request->input('ingreso_hasta');
        $ingresoBanco = $request->input('ingreso_banco');
        $ingresoCliente = $request->input('ingreso_cliente');

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

        $ventasFilters = [
            'desde' => $ventasDesde,
            'hasta' => $ventasHasta,
            'item' => $ventasItem,
        ];

        $comisionFilters = [
            'desde' => $comisionDesde,
            'hasta' => $comisionHasta,
            'empleado' => $comisionEmpleado,
            'cliente' => $comisionCliente,
        ];

        $gastoFilters = [
            'desde' => $gastoDesde,
            'hasta' => $gastoHasta,
            'proveedor' => $gastoProveedor,
            'responsable' => $gastoResponsable,
            'concepto' => $gastoConcepto,
        ];

        $ingresoFilters = [
            'desde' => $ingresoDesde,
            'hasta' => $ingresoHasta,
            'banco' => $ingresoBanco,
            'cliente' => $ingresoCliente,
        ];

        // === Chart: Ingresos vs Gastos ===
        $chartStart = Carbon::now()->subMonths(11)->startOfMonth();
        $chartEnd = Carbon::now()->endOfMonth();

        $ingresosPorMes = Pago::query()
            ->select(['fecha_hora', 'valor'])
            ->whereBetween('fecha_hora', [$chartStart, $chartEnd])
            ->get()
            ->groupBy(function (Pago $pago) {
                return optional($pago->fecha_hora)->format('Y-m');
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
                return optional($salida->fecha)->format('Y-m');
            })
            ->filter(function ($gastos, $periodo) {
                return filled($periodo);
            })
            ->mapWithKeys(function ($gastos, $periodo) {
                return [$periodo => $gastos->sum('valor')];
            });

        $chartLabels = [];
        $chartIngresos = [];
        $chartGastos = [];

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
        ]);
    }
}

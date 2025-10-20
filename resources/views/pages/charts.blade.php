@extends('layouts.vertical', ['subtitle' => 'Informes administrativos'])

@section('content')
<div class="row align-items-center mb-3">
    <div class="col-xl-8">
        <h2 class="fw-semibold mb-1">Reportes administrativos</h2>
        <p class="text-muted mb-0">Consulta y compara la información financiera y operativa de tu club desde un solo lugar.</p>
    </div>
    <div class="col-xl-4 text-xl-end mt-3 mt-xl-0">
        <a href="#ingresos-gastos" class="btn btn-soft-primary"><i class="bx bx-trending-up me-1"></i> Ver gráfica ingresos vs gastos</a>
    </div>
</div>

@if($pageError)
    <div class="alert alert-danger" role="alert">
        {{ $pageError }}
    </div>
@endif

<div class="row g-3 mb-5">
    <div class="col-6 col-md-4 col-xl-3">
        <a href="{{ route('pages.charts', ['tab' => 'ventas']) }}#ventas" class="card quick-report h-100 {{ $activeTab === 'ventas' ? 'border-primary shadow-sm' : 'border-light' }}">
            <div class="card-body d-flex flex-column justify-content-between">
                <div class="d-flex align-items-center mb-3">
                    <span class="avatar-sm flex-shrink-0 bg-primary-subtle text-primary rounded"><i class="bx bx-shopping-bag"></i></span>
                    <h6 class="ms-3 mb-0 text-uppercase text-muted">Ventas</h6>
                </div>
                <p class="fs-5 fw-semibold mb-0">{{ number_format($ventasTotal, 0, ',', '.') }} <small class="text-muted">total</small></p>
            </div>
        </a>
    </div>
    <div class="col-6 col-md-4 col-xl-3">
        <a href="{{ route('pages.charts', ['tab' => 'comisiones']) }}#comisiones" class="card quick-report h-100 {{ $activeTab === 'comisiones' ? 'border-primary shadow-sm' : 'border-light' }}">
            <div class="card-body d-flex flex-column justify-content-between">
                <div class="d-flex align-items-center mb-3">
                    <span class="avatar-sm flex-shrink-0 bg-success-subtle text-success rounded"><i class="bx bx-award"></i></span>
                    <h6 class="ms-3 mb-0 text-uppercase text-muted">Comisiones</h6>
                </div>
                <p class="fs-5 fw-semibold mb-0">{{ number_format($comisionesTotal, 0, ',', '.') }} <small class="text-muted">pagadas</small></p>
            </div>
        </a>
    </div>
    <div class="col-6 col-md-4 col-xl-3">
        <a href="{{ route('pages.charts', ['tab' => 'gastos']) }}#gastos" class="card quick-report h-100 {{ $activeTab === 'gastos' ? 'border-primary shadow-sm' : 'border-light' }}">
            <div class="card-body d-flex flex-column justify-content-between">
                <div class="d-flex align-items-center mb-3">
                    <span class="avatar-sm flex-shrink-0 bg-danger-subtle text-danger rounded"><i class="bx bx-receipt"></i></span>
                    <h6 class="ms-3 mb-0 text-uppercase text-muted">Gastos</h6>
                </div>
                <p class="fs-5 fw-semibold mb-0">{{ number_format($gastosTotal, 0, ',', '.') }} <small class="text-muted">registrados</small></p>
            </div>
        </a>
    </div>
    <div class="col-6 col-md-4 col-xl-3">
        <a href="{{ route('pages.charts', ['tab' => 'ingresos']) }}#ingresos" class="card quick-report h-100 {{ $activeTab === 'ingresos' ? 'border-primary shadow-sm' : 'border-light' }}">
            <div class="card-body d-flex flex-column justify-content-between">
                <div class="d-flex align-items-center mb-3">
                    <span class="avatar-sm flex-shrink-0 bg-warning-subtle text-warning rounded"><i class="bx bx-wallet"></i></span>
                    <h6 class="ms-3 mb-0 text-uppercase text-muted">Ingresos</h6>
                </div>
                <p class="fs-5 fw-semibold mb-0">{{ number_format($ingresosTotal, 0, ',', '.') }} <small class="text-muted">recibidos</small></p>
            </div>
        </a>
    </div>
    <div class="col-6 col-md-4 col-xl-2">
        <a href="#ingresos-gastos" class="card quick-report h-100 border-light">
            <div class="card-body d-flex flex-column justify-content-between">
                <div class="d-flex align-items-center mb-3">
                    <span class="avatar-sm flex-shrink-0 bg-secondary-subtle text-secondary rounded"><i class="bx bx-line-chart"></i></span>
                    <h6 class="ms-3 mb-0 text-uppercase text-muted">Ingresos/Gastos</h6>
                </div>
                <p class="fs-5 fw-semibold mb-0">Últimos 12 meses</p>
            </div>
        </a>
    </div>
</div>

<section id="ventas" class="mb-5">
    <div class="card">
        <div class="card-header border-0 pb-0">
            <div class="d-flex flex-column flex-md-row justify-content-between gap-3">
                <div>
                    <h4 class="mb-1">Ventas del período</h4>
                    <p class="text-muted mb-0">Consulta las ventas del mes actual y aplica filtros por rango de fechas o producto.</p>
                </div>
                <div class="text-md-end">
                    <span class="badge bg-primary-subtle text-primary">Total período: {{ number_format($ventasTotal, 0, ',', '.') }}</span>
                </div>
            </div>
        </div>
        <div class="card-body pt-3">
            <form method="GET" action="{{ route('pages.charts') }}#ventas" class="row g-3 align-items-end mb-4">
                <input type="hidden" name="tab" value="ventas">
                <div class="col-md-3">
                    <label class="form-label">Desde</label>
                    <input type="date" class="form-control" name="ventas_desde" value="{{ $ventasFilters['desde'] }}">
                </div>
                <div class="col-md-3">
                    <label class="form-label">Hasta</label>
                    <input type="date" class="form-control" name="ventas_hasta" value="{{ $ventasFilters['hasta'] }}">
                </div>
                <div class="col-md-4">
                    <label class="form-label">Producto</label>
                    <select class="form-select" name="ventas_item">
                        <option value="">Todos</option>
                        @foreach($items as $item)
                            <option value="{{ $item->id }}" @selected($ventasFilters['item'] == $item->id)>{{ $item->nombre }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="col-md-2 d-flex gap-2">
                    <button type="submit" class="btn btn-primary w-100">Filtrar</button>
                    <a href="{{ route('pages.charts', ['tab' => 'ventas']) }}#ventas" class="btn btn-soft-secondary" title="Limpiar filtros">
                        <i class="bx bx-reset"></i>
                    </a>
                </div>
            </form>

            <div class="table-responsive">
                <table class="table table-hover align-middle mb-0">
                    <thead class="table-light">
                        <tr>
                            <th>Fecha</th>
                            <th>Orden</th>
                            <th>Producto</th>
                            <th class="text-end">Cantidad</th>
                            <th class="text-end">Valor unitario</th>
                            <th class="text-end">Valor total</th>
                            <th>Cliente</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($ventas as $venta)
                            @php
                                $clienteVenta = optional($venta->orden)->clienterel;
                                $nombreClienteVenta = $clienteVenta ? trim(($clienteVenta->nombres ?? '') . ' ' . ($clienteVenta->apellidos ?? '')) : null;
                            @endphp
                            <tr>
                                <td>{{ optional($venta->created_at)->format('d/m/Y') }}</td>
                                <td>#{{ $venta->cuenta }}</td>
                                <td>{{ $venta->item->nombre ?? '—' }}</td>
                                <td class="text-end">{{ number_format($venta->cantidad, 0, ',', '.') }}</td>
                                <td class="text-end">{{ number_format($venta->valor_unitario, 0, ',', '.') }}</td>
                                <td class="text-end fw-semibold">{{ number_format($venta->valor_total, 0, ',', '.') }}</td>
                                <td>{{ $nombreClienteVenta ?: '—' }}</td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="7" class="text-center text-muted">No se encontraron ventas para los filtros seleccionados.</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            <div class="mt-3">
                {{ $ventas->links() }}
            </div>
        </div>
    </div>
</section>

<section id="comisiones" class="mb-5">
    <div class="card">
        <div class="card-header border-0 pb-0">
            <div class="d-flex flex-column flex-md-row justify-content-between gap-3">
                <div>
                    <h4 class="mb-1">Comisiones pagadas</h4>
                    <p class="text-muted mb-0">Visualiza los pagos de comisiones con la información del cliente y del colaborador responsable.</p>
                </div>
                <div class="text-md-end">
                    <span class="badge bg-success-subtle text-success">Total comisiones: {{ number_format($comisionesTotal, 0, ',', '.') }}</span>
                </div>
            </div>
        </div>
        <div class="card-body pt-3">
            <form method="GET" action="{{ route('pages.charts') }}#comisiones" class="row g-3 align-items-end mb-4">
                <input type="hidden" name="tab" value="comisiones">
                <div class="col-md-3">
                    <label class="form-label">Desde</label>
                    <input type="date" class="form-control" name="comision_desde" value="{{ $comisionFilters['desde'] }}">
                </div>
                <div class="col-md-3">
                    <label class="form-label">Hasta</label>
                    <input type="date" class="form-control" name="comision_hasta" value="{{ $comisionFilters['hasta'] }}">
                </div>
                <div class="col-md-3">
                    <label class="form-label">Empleado</label>
                    <select class="form-select" name="comision_empleado">
                        <option value="">Todos</option>
                        @foreach($empleados as $empleado)
                            <option value="{{ $empleado->id }}" @selected($comisionFilters['empleado'] == $empleado->id)>{{ $empleado->nombre }} {{ $empleado->apellidos }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="col-md-3">
                    <label class="form-label">Cliente</label>
                    <select class="form-select" name="comision_cliente">
                        <option value="">Todos</option>
                        @foreach($clientes as $cliente)
                            <option value="{{ $cliente->id }}" @selected($comisionFilters['cliente'] == $cliente->id)>{{ $cliente->nombres }} {{ $cliente->apellidos }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="col-12 d-flex gap-2 justify-content-end">
                    <button type="submit" class="btn btn-success">Filtrar</button>
                    <a href="{{ route('pages.charts', ['tab' => 'comisiones']) }}#comisiones" class="btn btn-soft-secondary" title="Limpiar filtros">
                        <i class="bx bx-reset"></i>
                    </a>
                </div>
            </form>

            <div class="table-responsive">
                <table class="table table-hover align-middle mb-0">
                    <thead class="table-light">
                        <tr>
                            <th>Fecha</th>
                            <th>Pago</th>
                            <th>Cliente</th>
                            <th>Empleado</th>
                            <th class="text-end">Valor</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($comisiones as $pago)
                            @php
                                $clienteComision = optional($pago->ordenDeCompra)->clienterel;
                                $nombreClienteComision = $clienteComision ? trim(($clienteComision->nombres ?? '') . ' ' . ($clienteComision->apellidos ?? '')) : null;
                                $empleadoComision = trim((optional($pago->responsableUsuario)->nombre ?? '') . ' ' . (optional($pago->responsableUsuario)->apellidos ?? ''));
                            @endphp
                            <tr>
                                <td>{{ optional($pago->fecha_hora)->format('d/m/Y') }}</td>
                                <td>#{{ $pago->id }}</td>
                                <td>{{ $nombreClienteComision ?: '—' }}</td>
                                <td>{{ $empleadoComision !== '' ? $empleadoComision : '—' }}</td>
                                <td class="text-end fw-semibold">{{ number_format($pago->valor, 0, ',', '.') }}</td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="5" class="text-center text-muted">No se encontraron comisiones para los filtros seleccionados.</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            <div class="mt-3">
                {{ $comisiones->links() }}
            </div>
        </div>
    </div>
</section>

<section id="gastos" class="mb-5">
    <div class="card">
        <div class="card-header border-0 pb-0">
            <div class="d-flex flex-column flex-md-row justify-content-between gap-3">
                <div>
                    <h4 class="mb-1">Gastos operativos</h4>
                    <p class="text-muted mb-0">Explora las salidas de dinero por proveedor, concepto o responsable.</p>
                </div>
                <div class="text-md-end">
                    <span class="badge bg-danger-subtle text-danger">Total gastos: {{ number_format($gastosTotal, 0, ',', '.') }}</span>
                </div>
            </div>
        </div>
        <div class="card-body pt-3">
            <form method="GET" action="{{ route('pages.charts') }}#gastos" class="row g-3 align-items-end mb-4">
                <input type="hidden" name="tab" value="gastos">
                <div class="col-md-3">
                    <label class="form-label">Desde</label>
                    <input type="date" class="form-control" name="gasto_desde" value="{{ $gastoFilters['desde'] }}">
                </div>
                <div class="col-md-3">
                    <label class="form-label">Hasta</label>
                    <input type="date" class="form-control" name="gasto_hasta" value="{{ $gastoFilters['hasta'] }}">
                </div>
                <div class="col-md-3">
                    <label class="form-label">Proveedor</label>
                    <select class="form-select" name="gasto_proveedor">
                        <option value="">Todos</option>
                        @foreach($proveedores as $proveedor)
                            <option value="{{ $proveedor->id }}" @selected($gastoFilters['proveedor'] == $proveedor->id)>{{ $proveedor->nombre }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="col-md-3">
                    <label class="form-label">Responsable</label>
                    <select class="form-select" name="gasto_responsable">
                        <option value="">Todos</option>
                        @foreach($empleados as $empleado)
                            <option value="{{ $empleado->id }}" @selected($gastoFilters['responsable'] == $empleado->id)>{{ $empleado->nombre }} {{ $empleado->apellidos }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="col-md-6">
                    <label class="form-label">Concepto</label>
                    <input type="text" class="form-control" name="gasto_concepto" placeholder="Buscar por concepto" value="{{ $gastoFilters['concepto'] }}">
                </div>
                <div class="col-md-6 d-flex gap-2 justify-content-end">
                    <button type="submit" class="btn btn-danger">Filtrar</button>
                    <a href="{{ route('pages.charts', ['tab' => 'gastos']) }}#gastos" class="btn btn-soft-secondary" title="Limpiar filtros">
                        <i class="bx bx-reset"></i>
                    </a>
                </div>
            </form>

            <div class="table-responsive">
                <table class="table table-hover align-middle mb-0">
                    <thead class="table-light">
                        <tr>
                            <th>Fecha</th>
                            <th>Concepto</th>
                            <th>Proveedor</th>
                            <th>Responsable</th>
                            <th class="text-end">Valor</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($gastos as $gasto)
                            @php
                                $responsableGasto = trim((optional($gasto->responsable)->nombre ?? '') . ' ' . (optional($gasto->responsable)->apellidos ?? ''));
                            @endphp
                            <tr>
                                <td>{{ optional($gasto->fecha)->format('d/m/Y') }}</td>
                                <td>{{ $gasto->concepto }}</td>
                                <td>{{ optional($gasto->tercero)->nombre ?? '—' }}</td>
                                <td>{{ $responsableGasto !== '' ? $responsableGasto : '—' }}</td>
                                <td class="text-end fw-semibold">{{ number_format($gasto->valor, 0, ',', '.') }}</td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="5" class="text-center text-muted">No se registran gastos para los filtros seleccionados.</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            <div class="mt-3">
                {{ $gastos->links() }}
            </div>
        </div>
    </div>
</section>

<section id="ingresos" class="mb-5">
    <div class="card">
        <div class="card-header border-0 pb-0">
            <div class="d-flex flex-column flex-md-row justify-content-between gap-3">
                <div>
                    <h4 class="mb-1">Ingresos registrados</h4>
                    <p class="text-muted mb-0">Controla los pagos recibidos por orden de compra, cliente y medio de pago.</p>
                </div>
                <div class="text-md-end">
                    <span class="badge bg-warning-subtle text-warning">Total ingresos: {{ number_format($ingresosTotal, 0, ',', '.') }}</span>
                </div>
            </div>
        </div>
        <div class="card-body pt-3">
            <form method="GET" action="{{ route('pages.charts') }}#ingresos" class="row g-3 align-items-end mb-4">
                <input type="hidden" name="tab" value="ingresos">
                <div class="col-md-3">
                    <label class="form-label">Desde</label>
                    <input type="date" class="form-control" name="ingreso_desde" value="{{ $ingresoFilters['desde'] }}">
                </div>
                <div class="col-md-3">
                    <label class="form-label">Hasta</label>
                    <input type="date" class="form-control" name="ingreso_hasta" value="{{ $ingresoFilters['hasta'] }}">
                </div>
                <div class="col-md-3">
                    <label class="form-label">Cliente</label>
                    <select class="form-select" name="ingreso_cliente">
                        <option value="">Todos</option>
                        @foreach($clientes as $cliente)
                            <option value="{{ $cliente->id }}" @selected($ingresoFilters['cliente'] == $cliente->id)>{{ $cliente->nombres }} {{ $cliente->apellidos }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="col-md-3">
                    <label class="form-label">Banco o caja</label>
                    <select class="form-select" name="ingreso_banco">
                        <option value="">Todos</option>
                        @foreach($bancos as $banco)
                            <option value="{{ $banco->id }}" @selected($ingresoFilters['banco'] == $banco->id)>{{ $banco->nombre }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="col-12 d-flex gap-2 justify-content-end">
                    <button type="submit" class="btn btn-warning">Filtrar</button>
                    <a href="{{ route('pages.charts', ['tab' => 'ingresos']) }}#ingresos" class="btn btn-soft-secondary" title="Limpiar filtros">
                        <i class="bx bx-reset"></i>
                    </a>
                </div>
            </form>

            <div class="table-responsive">
                <table class="table table-hover align-middle mb-0">
                    <thead class="table-light">
                        <tr>
                            <th>Fecha</th>
                            <th>Orden de compra</th>
                            <th>Cliente</th>
                            <th>Banco/Caja</th>
                            <th class="text-end">Valor</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($ingresos as $ingreso)
                            @php
                                $clienteIngreso = optional($ingreso->ordenDeCompra)->clienterel;
                                $nombreClienteIngreso = $clienteIngreso ? trim(($clienteIngreso->nombres ?? '') . ' ' . ($clienteIngreso->apellidos ?? '')) : null;
                            @endphp
                            <tr>
                                <td>{{ optional($ingreso->fecha_hora)->format('d/m/Y') }}</td>
                                <td>#{{ $ingreso->cuenta }}</td>
                                <td>{{ $nombreClienteIngreso ?: '—' }}</td>
                                <td>{{ $ingreso->bancoModel->nombre ?? 'Caja' }}</td>
                                <td class="text-end fw-semibold">{{ number_format($ingreso->valor, 0, ',', '.') }}</td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="5" class="text-center text-muted">No se encontraron ingresos para los filtros seleccionados.</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            <div class="mt-3">
                {{ $ingresos->links() }}
            </div>
        </div>
    </div>
</section>

@endsection

@push('styles')
<style>
    .quick-report {
        border-width: 1.5px;
        transition: all 0.2s ease-in-out;
    }

    .quick-report:hover {
        transform: translateY(-4px);
        box-shadow: 0 0.875rem 1.875rem rgba(38, 43, 72, 0.1);
    }

    .quick-report .avatar-sm {
        width: 42px;
        height: 42px;
        display: inline-flex;
        align-items: center;
        justify-content: center;
        font-size: 1.25rem;
    }
</style>
@endpush


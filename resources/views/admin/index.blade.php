@extends('layouts.vertical', ['subtitle' => 'Inicio'])

@section('content')

@include('layouts.partials/page-title', ['title' => 'Dashboard', 'subtitle' => 'Inicio'])

<div class="row">
    <div class="col-12">
        <div class="card border-0 shadow-sm bg-soft-primary mb-4">
            <div class="card-body d-flex flex-wrap align-items-start gap-4">
                <div class="flex-grow-1">
                    <p class="text-uppercase text-muted fw-semibold mb-1">Hoy</p>
                    <h3 class="fw-semibold text-dark mb-3">{{ ucfirst($fechaHoyLegible) }}</h3>
                    <div class="d-flex flex-column gap-2">
                        <div class="d-flex justify-content-between align-items-center">
                            <span class="text-muted">Ingresos cobrados hoy</span>
                            <span class="fw-semibold fs-5 text-dark">${{ number_format($pagosHoy, 0, ',', '.') }}</span>
                        </div>
                        <div class="d-flex justify-content-between align-items-center">
                            <span class="text-muted">Ingresos agendados pendientes hoy</span>
                            <span class="fw-semibold fs-6 text-dark">${{ number_format($ingresosPendientesHoy, 0, ',', '.') }}</span>
                        </div>
                        <div>
                            <span class="text-muted d-block">Asistencia</span>
                            <span class="fw-semibold fs-5 text-dark">{{ $asistenciaPorcentaje }}%</span>
                            <small class="text-success d-block">
                                ({{ $ausenciasRecuperadas }} ausencia{{ $ausenciasRecuperadas === 1 ? '' : 's' }} recuperada{{ $ausenciasRecuperadas === 1 ? '' : 's' }} con recordatorio WhatsApp ✅)
                            </small>
                        </div>
                    </div>
                </div>
                <div class="ms-auto">
                    <div class="bg-white border rounded-3 p-3 shadow-sm text-end">
                        <p class="text-muted mb-1">Citas confirmadas hoy</p>
                        <h4 class="mb-2">{{ $confirmadasHoy }} / {{ $totalAgendadasHoy }}</h4>
                        <span class="badge bg-soft-primary text-primary">Huecos libres: {{ $totalHuecosDisponibles }}</span>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<div class="row g-3 mb-4">
    <div class="col-md-6 col-xl-4">
        <div class="card h-100">
            <div class="card-body d-flex align-items-center">
                <div class="flex-grow-1">
                    <p class="text-muted text-uppercase fw-semibold mb-1">Caja de hoy</p>
                    <h3 class="mb-2">${{ number_format($pagosHoy, 0, ',', '.') }}</h3>
                    @php
                        $variacionSigno = $variacionCaja >= 0 ? '↑' : '↓';
                        $variacionClase = $variacionCaja >= 0 ? 'bg-soft-success text-success' : 'bg-soft-danger text-danger';
                    @endphp
                    <span class="badge {{ $variacionClase }}">
                        {{ $variacionSigno }} ${{ number_format(abs($variacionCaja), 0, ',', '.') }} vs ayer
                    </span>
                </div>
                <div class="ms-3 avatar-md bg-soft-primary rounded d-flex align-items-center justify-content-center">
                    <iconify-icon icon="solar:scissors-linear" class="fs-32 text-primary"></iconify-icon>
                </div>
            </div>
            <div id="chart01"></div>
        </div>
    </div>
    <div class="col-md-6 col-xl-4">
        <div class="card h-100">
            <div class="card-body d-flex align-items-center">
                <div class="flex-grow-1">
                    <p class="text-muted text-uppercase fw-semibold mb-1">Clientes registrados</p>
                    <h3 class="mb-0">{{ number_format($totalClientes) }}</h3>
                </div>
                <div class="ms-3 avatar-md bg-soft-primary rounded d-flex align-items-center justify-content-center">
                    <iconify-icon icon="solar:users-group-two-rounded-broken" class="fs-32 text-primary"></iconify-icon>
                </div>
            </div>
            <div id="chart02"></div>
        </div>
    </div>
    <div class="col-md-6 col-xl-4">
        <div class="card h-100">
            <div class="card-body d-flex align-items-center">
                <div class="flex-grow-1">
                    <p class="text-muted text-uppercase fw-semibold mb-1">Citas del mes</p>
                    <h3 class="mb-0">{{ $totalReservas }}</h3>
                </div>
                <div class="ms-3 avatar-md bg-soft-primary rounded d-flex align-items-center justify-content-center">
                    <iconify-icon icon="solar:calendar-outline" class="fs-32 text-primary"></iconify-icon>
                </div>
            </div>
            <div id="chart03"></div>
        </div>
    </div>
</div>

<div class="row g-3 mb-4">
    <div class="col-xl-4">
        <div class="card h-100">
            <div class="card-header border-0 pb-0">
                <h4 class="card-title mb-0">Agenda de hoy</h4>
            </div>
            <div class="card-body">
                <ul class="list-unstyled mb-4 d-flex flex-column gap-2">
                    <li class="d-flex align-items-center gap-2">
                        <span class="fs-4">💵</span>
                        <span class="fw-semibold">Total cobrado hoy:</span>
                        <span class="ms-auto fw-bold text-dark">${{ number_format($pagosHoy, 0, ',', '.') }}</span>
                    </li>
                    <li class="d-flex align-items-center gap-2">
                        <span class="fs-4">📅</span>
                        <span>Citas para hoy:</span>
                        <span class="ms-auto fw-semibold text-dark">{{ $confirmadasHoy }} / {{ $totalAgendadasHoy }} confirmadas</span>
                    </li>
                    <li class="d-flex align-items-center gap-2">
                        <span class="fs-4">⏳</span>
                        <span>Huecos libres esta tarde:</span>
                        <span class="ms-auto fw-semibold text-dark">{{ $totalHuecosDisponibles }}</span>
                    </li>
                    <li class="d-flex align-items-center gap-2">
                        <span class="fs-4">🚫</span>
                        <span>Ausencias:</span>
                        <span class="ms-auto fw-semibold text-dark">{{ $ausenciasHoy }}</span>
                    </li>
                </ul>
                <a href="{{ route('clientes.reengage') }}" class="btn btn-success btn-lg w-100">
                    Llenar huecos por WhatsApp
                </a>
            </div>
        </div>
    </div>
    <div class="col-xl-4">
        <div class="card h-100">
            <div class="card-header border-0 pb-0">
                <h4 class="card-title mb-0">Rendimiento del Equipo - Hoy</h4>
            </div>
            <div class="card-body">
                <ul class="list-unstyled mb-0">
                    @forelse ($teamPerformance as $registro)
                        @php
                            $barbero = optional($registro->barbero)->nombre_completo ?? 'Sin asignar';
                        @endphp
                        <li class="d-flex justify-content-between align-items-center py-2 border-bottom">
                            <div class="d-flex align-items-center gap-2">
                                <span class="fs-4">💈</span>
                                <div>
                                    <span class="fw-semibold">{{ $barbero }}</span>
                                    <div class="text-muted small">{{ $registro->servicios }} servicio{{ $registro->servicios == 1 ? '' : 's' }}</div>
                                </div>
                            </div>
                            <span class="fw-semibold text-dark">${{ number_format((int) $registro->total_cobrado, 0, ',', '.') }}</span>
                        </li>
                    @empty
                        <li class="py-4 text-center text-muted">
                            Aún no hay ventas registradas hoy.
                        </li>
                    @endforelse
                </ul>
            </div>
            <div class="card-footer bg-light border-0 d-flex justify-content-between align-items-center">
                <span class="fw-semibold text-uppercase">TOTAL HOY</span>
                <span class="fw-bold text-dark">${{ number_format($totalEquipoHoy, 0, ',', '.') }}</span>
            </div>
        </div>
    </div>
    <div class="col-xl-4">
        <div class="card h-100">
            <div class="card-header border-0 pb-0">
                <h4 class="card-title mb-0">Huecos Libres de Hoy</h4>
            </div>
            <div class="card-body">
                <p class="text-muted">Quedan {{ $totalHuecosDisponibles }} espacio{{ $totalHuecosDisponibles == 1 ? '' : 's' }} libre{{ $totalHuecosDisponibles == 1 ? '' : 's' }}:</p>
                <div class="list-group list-group-flush">
                    @forelse ($huecosDestacados as $hueco)
                        <div class="list-group-item px-0 d-flex justify-content-between align-items-center">
                            <div>
                                <h5 class="mb-1">{{ $hueco->inicio->format('H:i') }}</h5>
                                <p class="text-muted mb-0">{{ $hueco->servicio }} ({{ $hueco->barbero }})</p>
                            </div>
                            <span class="badge bg-soft-primary text-primary">{{ $hueco->duracion }} min</span>
                        </div>
                    @empty
                        <div class="text-muted py-4 text-center">
                            Agenda completa por ahora.
                        </div>
                    @endforelse
                </div>
            </div>
            <div class="card-footer bg-light border-0">
                <a href="{{ route('clientes.reengage') }}" class="btn btn-outline-success w-100">
                    Compartir huecos por WhatsApp
                </a>
            </div>
        </div>
    </div>
</div>

<div class="row">
    <div class="col-xl-6">
        <div class="card">
            <div class="card-header d-flex justify-content-between align-items-center">
                <h4 class="card-title mb-0">Nuevos Clientes</h4>
                <a href="{{ route('clientes.index') }}" class="btn btn-sm btn-light">Ver todos</a>
            </div>
            <div class="card-body pb-1">
                <div class="table-responsive">
                    <table class="table table-hover mb-0 table-centered">
                        <thead>
                            <tr>
                                <th class="py-1">ID</th>
                                <th class="py-1">Nombres</th>
                                <th class="py-1">WhatsApp</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse ($clientes as $cliente)
                                <tr>
                                    <td>{{ $loop->iteration }}</td>
                                    <td><a href="{{ route('clientes.show', $cliente) }}">{{ $cliente->nombres }} {{ $cliente->apellidos }}</a></td>
                                    <td>
                                        @php
                                            $clean = preg_replace('/\D+/', '', $cliente->whatsapp);
                                        @endphp
                                        <a href="https://wa.me/{{ $clean }}" target="_blank">{{ $cliente->whatsapp }}</a>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="3" class="text-center">Aún no hay clientes</td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
    <div class="col-xl-6">
        <div class="card">
            <div class="card-header d-flex justify-content-between align-items-center">
                <h4 class="card-title mb-0">Transacciones recientes</h4>
                <a href="{{ route('orden_de_compras.index') }}" class="btn btn-sm btn-light">Ver todas</a>
            </div>
            <div class="card-body">
                <div class="table-responsive">
                    <table class="table table-hover mb-0 table-centered">
                        <thead>
                            <tr>
                                <th class="py-1">ID</th>
                                <th class="py-1">Fecha</th>
                                <th class="py-1">Cliente</th>
                                <th class="py-1">Monto</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse ($cuentas as $cuenta)
                                <tr>
                                    <td>{{ $loop->iteration }}</td>
                                    <td><a href="{{ route('orden_de_compras.show', $cuenta) }}">{{ $cuenta->fecha_hora->format('d/m/Y H:i') }}</a></td>
                                    <td>{{ $cuenta->clienterel->nombres }} {{ $cuenta->clienterel->apellidos }}</td>
                                    <td>${{ number_format($cuenta->monto, 0, ',', '.') }}</td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="4" class="text-center">Sin cuentas</td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection


@extends('layouts.vertical', ['subtitle' => 'Detalle de Orden de Compra'])

@section('content')
<div class="container py-3">

    {{-- Encabezado + Acciones --}}
    <div class="d-flex align-items-center justify-content-between mb-3">
        <div>
            <h1 class="h3 mb-1">Orden de Compra #{{ $orden->id }}</h1>
            @php
                $activa = $orden->activa ? 'Sí' : 'No';
                $badgeClass = $orden->activa ? 'bg-success' : 'bg-secondary';
            @endphp
            <span class="badge {{ $badgeClass }} px-2 py-1">Activa: {{ $activa }}</span>
        </div>
        <div class="btn-group">
            <a class="btn btn-outline-secondary"
               href="{{ route('ordenes.pdf', $orden) }}"
               target="_blank" rel="noopener">
                Descargar PDF
            </a>
            <button class="btn btn-primary"
                    data-bs-toggle="modal"
                    data-bs-target="#enviarCorreoModal">
                Enviar por correo
            </button>
        </div>
    </div>

    {{-- Resumen de totales --}}
    @php
        $subtotal = optional($orden->ventas)->sum('valor_total') ?? 0;
        $pagado   = optional($orden->pagos)->sum('valor') ?? 0;
        $saldo    = max(0, $subtotal - $pagado);
        $fmt = fn($v) => number_format($v, 0, ',', '.');
    @endphp

    <div class="row g-3 mb-4">
        <div class="col-md-4">
            <div class="card shadow-sm h-100">
                <div class="card-body">
                    <div class="text-muted small">Subtotal</div>
                    <div class="fs-4 fw-semibold">COP {{ $fmt($subtotal) }}</div>
                </div>
            </div>
        </div>
        <div class="col-md-4">
            <div class="card shadow-sm h-100">
                <div class="card-body">
                    <div class="text-muted small">Pagado</div>
                    <div class="fs-4 fw-semibold">COP {{ $fmt($pagado) }}</div>
                </div>
            </div>
        </div>
        <div class="col-md-4">
            @php
                $saldoClass = $saldo <= 0 ? 'border-success' : 'border-warning';
                $saldoText  = $saldo <= 0 ? 'text-success' : 'text-warning';
                $estadoPago = $saldo <= 0 ? 'Pagada' : 'Saldo pendiente';
            @endphp
            <div class="card shadow-sm h-100 border {{ $saldoClass }}">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-center">
                        <div>
                            <div class="text-muted small">Saldo</div>
                            <div class="fs-4 fw-semibold {{ $saldoText }}">COP {{ $fmt($saldo) }}</div>
                        </div>
                        <span class="badge {{ $saldo <= 0 ? 'bg-success' : 'bg-warning text-dark' }}">
                            {{ $estadoPago }}
                        </span>
                    </div>
                </div>
            </div>
        </div>
    </div>

    {{-- Información de la Orden --}}
    <div class="card mb-4 shadow-sm">
        <div class="card-header">
            <h5 class="mb-0">Información de la Orden</h5>
        </div>
        <div class="card-body">
            @php
                $tz = 'America/Bogota';
                $fh = $orden->fecha_hora ? \Carbon\Carbon::parse($orden->fecha_hora)->timezone($tz)->format('d/m/Y H:i') : '—';
                $creado = $orden->created_at ? $orden->created_at->timezone($tz)->format('d/m/Y H:i') : '—';
                $actualizado = $orden->updated_at ? $orden->updated_at->timezone($tz)->format('d/m/Y H:i') : '—';
            @endphp
            <div class="row g-3">
                <div class="col-md-4">
                    <p class="mb-1"><strong>Fecha y hora:</strong> {{ $fh }}</p>
                    <p class="mb-1"><strong>Responsable:</strong> {{ $orden->responsable ?? '—' }}</p>
                    <p class="mb-0"><strong>Activa:</strong> {{ $orden->activa ? 'Sí' : 'No' }}</p>
                </div>
                <div class="col-md-4">
                    <p class="mb-1"><strong>Creado:</strong> {{ $creado }}</p>
                    <p class="mb-0"><strong>Actualizado:</strong> {{ $actualizado }}</p>
                </div>
            </div>
        </div>
    </div>

    {{-- Cliente --}}
    <div class="card mb-4 shadow-sm">
        <div class="card-header">
            <h5 class="mb-0">Cliente</h5>
        </div>
        <div class="card-body">
            @if(is_array($orden->alumno) || is_object($orden->alumno))
                <p class="mb-1"><strong>ID:</strong> {{ data_get($orden->alumno, 'numero_identificacion', '—') }}</p>
                <p class="mb-1"><strong>Nombre:</strong> {{ trim((data_get($orden->alumno, 'nombres', '')) . ' ' . (data_get($orden->alumno, 'apellidos', ''))) ?: '—' }}</p>
                <p class="mb-1"><strong>Correo:</strong> {{ data_get($orden->alumno, 'correo', '—') }}</p>
                <p class="mb-0"><strong>WhatsApp:</strong> {{ data_get($orden->alumno, 'whatsapp', '—') }}</p>
            @else
                {{-- Si es string/JSON plano guardado --}}
                <p class="mb-0">{{ $orden->alumno ?: '—' }}</p>
            @endif
        </div>
    </div>

    {{-- Ventas --}}
    <div class="card mb-4 shadow-sm">
        <div class="card-header d-flex justify-content-between align-items-center">
            <h5 class="mb-0">Ventas</h5>
        </div>
        <div class="card-body p-0">
            @if($orden->ventas && $orden->ventas->count())
                <div class="table-responsive">
                    <table class="table table-hover table-sm align-middle mb-0">
                        <thead class="table-light">
                            <tr>
                                <th>ID</th>
                                <th>Artículo</th>
                                <th class="text-end">Cantidad</th>
                                <th class="text-end">Descuento</th>
                                <th class="text-end">Precio</th>
                                <th class="text-end">Total</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($orden->ventas as $venta)
                                <tr>
                                    <td>{{ $venta->id }}</td>
                                    <td>{{ optional($venta->item)->nombre ?? '—' }}</td>
                                    <td class="text-end">{{ $venta->cantidad }}</td>
                                    <td class="text-end">{{ rtrim(rtrim(number_format($venta->descuento ?? 0, 2, ',', '.'), '0'), ',') }}%</td>
                                    <td class="text-end">COP {{ $fmt($venta->valor_unitario) }}</td>
                                    <td class="text-end fw-semibold">COP {{ $fmt($venta->valor_total) }}</td>
                                </tr>
                            @endforeach
                        </tbody>
                        <tfoot class="table-light">
                            <tr>
                                <th colspan="5" class="text-end">Subtotal</th>
                                <th class="text-end">COP {{ $fmt($subtotal) }}</th>
                            </tr>
                        </tfoot>
                    </table>
                </div>
            @else
                <p class="p-3 mb-0 text-muted">No hay ventas asociadas a esta orden.</p>
            @endif
        </div>
    </div>

    {{-- Pagos --}}
    <div class="card mb-4 shadow-sm">
        <div class="card-header">
            <h5 class="mb-0">Pagos</h5>
        </div>
        <div class="card-body p-0">
            @if($orden->pagos && $orden->pagos->count())
                <div class="table-responsive">
                    <table class="table table-hover table-sm align-middle mb-0">
                        <thead class="table-light">
                            <tr>
                                <th>ID</th>
                                <th>Fecha</th>
                                <th>Método</th>
                                <th class="text-end">Valor</th>
								<th>Acciones</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($orden->pagos as $pago)
                                @php
                                    $fp = $pago->created_at ? $pago->created_at->timezone($tz)->format('d/m/Y H:i') : '—';
                                @endphp
                                <tr>
                                    <td>{{ $pago->id }}</td>
                                    <td>{{ $fp }}</td>
                                    <td>{{ ucfirst($pago->metodo) }}</td>
                                    <td class="text-end">COP {{ $fmt($pago->valor) }}</td>
									<td>
                <a href="{{ route('pagos.edit', $pago) }}" class="btn btn-sm btn-primary">

                  <i class='bx bx-edit'></i>

                </a>
              </td>
                                </tr>
                            @endforeach
                        </tbody>
                        <tfoot class="table-light">
                            <tr>
                                <th colspan="3" class="text-end">Total pagado</th>
                                <th class="text-end">COP {{ $fmt($pagado) }}</th>
                            </tr>
                        </tfoot>
                    </table>
                </div>
            @else
                <p class="p-3 mb-0 text-muted">No hay pagos registrados para esta orden.</p>
            @endif
        </div>
    </div>

    {{-- Modal Enviar por correo --}}
    @php
        $emailSugerido = is_array($orden->alumno) || is_object($orden->alumno)
            ? data_get($orden->alumno, 'correo')
            : null;
    @endphp
    <div class="modal fade" id="enviarCorreoModal" tabindex="-1" aria-labelledby="enviarCorreoModalLabel" aria-hidden="true">
        <div class="modal-dialog">
            <form class="modal-content" method="POST" action="{{ route('ordenes.email', $orden) }}">
                @csrf
                <div class="modal-header">
                    <h5 class="modal-title" id="enviarCorreoModalLabel">Enviar orden por correo</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Cerrar"></button>
                </div>
                <div class="modal-body">
                    <div class="mb-3">
                        <label for="correoDestino" class="form-label">Correo destino</label>
                        <input type="email" class="form-control" id="correoDestino" name="to"
                               value="{{ old('to', $emailSugerido) }}" required>
                        <div class="form-text">Se enviará la orden en PDF adjunta.</div>
                    </div>
                    <div class="mb-3">
                        <label for="mensajeOpcional" class="form-label">Mensaje (opcional)</label>
                        <textarea class="form-control" id="mensajeOpcional" name="mensaje" rows="3"
                                  placeholder="Hola, te compartimos la Orden de Compra #{{ $orden->id }}."></textarea>
                    </div>
                </div>
                <div class="modal-footer">
                    <button class="btn btn-outline-secondary" type="button" data-bs-dismiss="modal">Cancelar</button>
                    <button class="btn btn-primary" type="submit">Enviar</button>
                </div>
            </form>
        </div>
    </div>

    {{-- Alertas de estado --}}
    @if(session('success'))
        <div class="alert alert-success mt-3">{{ session('success') }}</div>
    @endif
    @if($errors->any())
        <div class="alert alert-danger mt-3">
            <ul class="mb-0">
                @foreach($errors->all() as $err) <li>{{ $err }}</li> @endforeach
            </ul>
        </div>
    @endif

</div>
@endsection

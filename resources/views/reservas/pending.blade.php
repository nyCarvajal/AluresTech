@extends('layouts.vertical', ['subtitle' => 'Reservas pendientes'])

@section('content')
<div class="row">
    <div class="col-12">
        <div class="card">
            <div class="card-header d-flex justify-content-between align-items-center">
                <div>
                    <h5 class="mb-1">Solicitudes pendientes de confirmación</h5>
                    <p class="mb-0 text-muted">Revisa y confirma las citas solicitadas desde el portal público.</p>
                </div>
                <a href="{{ route('reservas.calendar') }}" class="btn btn-outline-primary">Ver calendario</a>
            </div>
            <div class="card-body">
                @if ($errors->any())
                    <div class="alert alert-danger">
                        <p class="mb-1 fw-semibold">No se pudo procesar la solicitud:</p>
                        <ul class="mb-0 ps-3">
                            @foreach ($errors->all() as $error)
                                <li>{{ $error }}</li>
                            @endforeach
                        </ul>
                    </div>
                @endif
                @if (session('success'))
                    <div class="alert alert-success">{{ session('success') }}</div>
                @endif
                @if (session('error'))
                    <div class="alert alert-danger">{{ session('error') }}</div>
                @endif

                <div class="table-responsive">
                    <table class="table table-hover align-middle">
                        <thead class="table-light">
                            <tr>
                                <th>Fecha y hora</th>
                                <th>Cliente</th>
                                <th>Servicio</th>
                                <th>Duración</th>
                                <th>Notas del cliente</th>
                                <th>Acciones</th>
                            </tr>
                        </thead>
                        <tbody>
                        @forelse ($reservas as $reserva)
                            <tr>
                                <td>
                                    <div class="fw-semibold">{{ \Carbon\Carbon::parse($reserva->fecha)->format('d/m/Y H:i') }}</div>
                                    <span class="badge bg-warning text-dark">{{ $reserva->estado }}</span>
                                </td>
                                <td>
                                    <div class="fw-semibold">{{ optional($reserva->cliente)->nombres }} {{ optional($reserva->cliente)->apellidos }}</div>
                                    <small class="text-muted">{{ optional($reserva->cliente)->correo }}</small>
                                </td>
                                <td>{{ $reserva->tipo ?? 'Reserva' }}</td>
                                <td>{{ $reserva->duracion }} min</td>
                                <td>
                                    @if ($reserva->nota_cliente)
                                        <span class="text-break">{{ $reserva->nota_cliente }}</span>
                                    @else
                                        <span class="text-muted">Sin comentarios</span>
                                    @endif
                                </td>
                                <td>
                                    <div class="d-flex gap-2">
                                        <form method="POST" action="{{ route('reservas.pending.confirm', $reserva) }}">
                                            @csrf
                                            <button type="submit" class="btn btn-success btn-sm">Confirmar</button>
                                        </form>
                                        <form method="POST" action="{{ route('reservas.update', $reserva) }}">
                                            @csrf
                                            @method('PUT')
                                            <input type="hidden" name="type" value="{{ $reserva->type ?? 'Reserva' }}">
                                            <input type="hidden" name="start" value="{{ $reserva->fecha }}">
                                            <input type="hidden" name="duration" value="{{ $reserva->duracion }}">
                                            <input type="hidden" name="estado" value="Cancelada">
                                            <input type="hidden" name="cancha_id" value="{{ $reserva->cancha_id }}">
                                            <input type="hidden" name="cliente_id" value="{{ $reserva->cliente_id }}">
                                            <input type="hidden" name="entrenador_id" value="{{ $reserva->entrenador_id }}">
                                            <button type="submit" class="btn btn-outline-danger btn-sm">Rechazar</button>
                                        </form>
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="6" class="text-center py-4 text-muted">No hay solicitudes pendientes por confirmar.</td>
                            </tr>
                        @endforelse
                        </tbody>
                    </table>
                </div>

                <div class="mt-3">
                    {{ $reservas->links() }}
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

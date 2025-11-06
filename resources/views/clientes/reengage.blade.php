@extends('layouts.vertical', ['subtitle' => 'Recuperar clientes'])

@section('content')
@include('layouts.partials/page-title', [
    'title' => 'Llenar huecos por WhatsApp',
    'subtitle' => 'Clientes inactivos'
])

<div class="card">
    <div class="card-header d-flex flex-wrap justify-content-between align-items-center gap-2">
        <div>
            <h4 class="card-title mb-1">Contacta a quienes no reservan hace más de un mes</h4>
            <p class="mb-0 text-muted">
                Revisamos a los clientes sin reservas desde antes del {{ $threshold->locale(app()->getLocale())->translatedFormat('d \d\e F Y') }}.
            </p>
        </div>
        <a href="{{ route('dashboard') }}" class="btn btn-outline-secondary">
            Volver al dashboard
        </a>
    </div>

    <div class="card-body">
        <div class="alert alert-soft-primary" role="alert">
            <div class="d-flex align-items-start">
                <div class="me-2">
                    <i class="ri-whatsapp-line fs-24 text-success"></i>
                </div>
                <div>
                    <h5 class="alert-heading mb-1">Mensaje sugerido</h5>
                    <p class="mb-0">{{ $mensajeBase }}</p>
                    <small class="text-muted">Personalizamos automáticamente el nombre y el de tu barbería.</small>
                </div>
            </div>
        </div>

        <div class="table-responsive">
            <table class="table table-hover table-centered mb-0">
                <thead class="table-light">
                    <tr>
                        <th>Cliente</th>
                        <th>Última reserva</th>
                        <th>WhatsApp</th>
                        <th class="text-end">Acción</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse ($clientes as $cliente)
                        @php
                            $ultimaReserva = optional($cliente->reservas->first())->fecha;
                            $ultimaReservaTexto = $ultimaReserva
                                ? \Carbon\Carbon::parse($ultimaReserva)->locale(app()->getLocale())->translatedFormat('d \d\e F H:i')
                                : 'Nunca';

                            $cleanNumber = preg_replace('/\D+/', '', $cliente->whatsapp ?? '');
                            $nombreCliente = trim($cliente->nombres . ' ' . ($cliente->apellidos ?? ''));
                            $nombrePeluqueria = optional(optional(Auth::user())->peluqueria)->nombre ?? config('app.name');

                            $mensaje = $mensajeBase;
                            $mensaje = str_replace(['{{nombre}}', '{{ Nombre }}', '{{NOMBRE}}'], $cliente->nombres, $mensaje);
                            $mensaje = str_replace(['{{cliente}}', '{{ Cliente }}'], $nombreCliente, $mensaje);
                            $mensaje = str_replace(['{{negocio}}', '{{peluqueria}}', '{{ Peluqueria }}', '{{ negocio }}'], $nombrePeluqueria, $mensaje);
                            $mensaje = trim(preg_replace('/\s+/', ' ', $mensaje));
                        @endphp
                        <tr>
                            <td>
                                <span class="fw-semibold">{{ $nombreCliente }}</span>
                            </td>
                            <td>{{ $ultimaReservaTexto }}</td>
                            <td>{{ $cliente->whatsapp ?? 'Sin número' }}</td>
                            <td class="text-end">
                                @if ($cleanNumber)
                                    <a
                                        class="btn btn-success btn-sm"
                                        target="_blank"
                                        href="https://wa.me/{{ $cleanNumber }}?text={{ urlencode($mensaje) }}"
                                    >
                                        Enviar recordatorio
                                    </a>
                                @else
                                    <span class="badge bg-soft-warning text-warning">WhatsApp faltante</span>
                                @endif
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="4" class="text-center text-muted py-4">
                                ¡Genial! Todos tus clientes han reservado durante el último mes.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>
@endsection

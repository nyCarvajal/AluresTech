@extends('layouts.vertical', ['subtitle' => 'Cumpleaños'])

@section('content')
<div class="card">
    <div class="card-header d-flex flex-column flex-md-row align-items-md-center justify-content-between gap-2">
        <div>
            <h5 class="card-title mb-1">Cumpleaños de Hoy</h5>
            <p class="card-subtitle">Clientes que celebran su cumpleaños el {{ $today->translatedFormat('d \d\e F') }}.</p>
        </div>
        <a href="{{ route('clientes.index') }}" class="btn btn-outline-secondary">Ver todos los clientes</a>
    </div>
    <div class="card-body">
        @if($clientes->isEmpty())
            <div class="text-center py-5">
                <iconify-icon icon="solar:confetti-outline" class="display-3 text-muted"></iconify-icon>
                <p class="mt-3 mb-0">No hay clientes que cumplan años hoy.</p>
            </div>
        @else
            <div class="table-responsive">
                <table class="table table-hover table-centered align-middle">
                    <thead class="table-light">
                        <tr>
                            <th scope="col">Cliente</th>
                            <th scope="col">Última visita</th>
                            <th scope="col">WhatsApp</th>
                            <th scope="col" class="text-end">Acciones</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($clientes as $cliente)
                            @php
                                $lastReservation = $cliente->reservas->first();
                                $lastVisit = $lastReservation?->fecha;
                                $whatsappNumber = preg_replace('/\D+/', '', $cliente->whatsapp ?? '');
                            @endphp
                            <tr>
                                <td>
                                    <div class="d-flex align-items-center gap-2">
                                        <img
                                            src="{{ $cliente->foto
                                                ? 'https://res.cloudinary.com/dpikkji3p/image/upload/' . $cliente->foto . '.jpg'
                                                : ($cliente->sexo === 'F'
                                                    ? asset('images/users/avatar-2.jpg')
                                                    : asset('images/users/avatar-1.jpg'))
                                            }}"
                                            alt="Foto de {{ $cliente->nombres }} {{ $cliente->apellidos }}"
                                            class="avatar-sm rounded-circle">
                                        <div>
                                            <h5 class="mb-0">{{ $cliente->nombres }} {{ $cliente->apellidos }}</h5>
                                            <small class="text-muted">{{ $cliente->tipo_identificacion }} • {{ $cliente->numero_identificacion }}</small>
                                        </div>
                                    </div>
                                </td>
                                <td>
                                    @if($lastVisit)
                                        {{ \Carbon\Carbon::parse($lastVisit)->translatedFormat('d \d\e F, Y') }}
                                    @else
                                        <span class="text-muted">Sin visitas registradas</span>
                                    @endif
                                </td>
                                <td>
                                    @if($whatsappNumber)
                                        <a href="https://wa.me/{{ $whatsappNumber }}" target="_blank">
                                            {{ $cliente->whatsapp }}
                                        </a>
                                    @else
                                        <span class="text-muted">No registrado</span>
                                    @endif
                                </td>
                                <td class="text-end">
                                    @if($whatsappNumber)
                                        <a
                                            href="https://wa.me/{{ $whatsappNumber }}?text={{ urlencode('¡Hola ' . $cliente->nombres . '! Feliz cumpleaños de parte de todo el equipo.') }}"
                                            target="_blank"
                                            class="btn btn-success btn-sm">
                                            Enviar mensaje por WhatsApp
                                        </a>
                                    @else
                                        <button type="button" class="btn btn-success btn-sm" disabled>
                                            Enviar mensaje por WhatsApp
                                        </button>
                                    @endif
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        @endif
    </div>
</div>
@endsection

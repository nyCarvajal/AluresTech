@extends('layouts.vertical', ['subtitle' => 'Peluqueria'])

@section('content')
<div class="container">
    <h1>Perfil del Peluqueria</h1>

    <div class="card mb-4">
        <div class="card-body">
            <p><strong>Nombre:</strong> {{ $peluqueria->nombre }}</p>
            <p><strong>POS:</strong> {{ $peluqueria->pos ? 'Sí' : 'No' }}</p>
            <p><strong>Cuenta de Cobro:</strong> {{ $peluqueria->cuentaCobro ? 'Sí' : 'No' }}</p>
            <p><strong>Facturación Electrónica:</strong> {{ $peluqueria->electronica ? 'Sí' : 'No' }}</p>
            <p><strong>Términos:</strong></p>
            <div class="border p-2 mb-3" style="white-space: pre-wrap;">{{ $peluqueria->terminos }}</div>

            <p><strong>Mensaje Reserva Confirmada:</strong></p>
            <div class="border p-2 mb-3" style="white-space: pre-wrap;">{{ $peluqueria->msj_reserva_confirmada }}</div>
            <p><strong>Mensaje de Bienvenida:</strong></p>
            <div class="border p-2 mb-3" style="white-space: pre-wrap;">{{ $peluqueria->msj_bienvenida }}</div>
            <p><strong>Mensaje de Recordatorio:</strong></p>
            <div class="border p-2 mb-3" style="white-space: pre-wrap;">{{ $peluqueria->msj_finalizado }}</div>
            <p><strong>Mensaje de paquete finjalizado:</strong></p>
            <div class="border p-2 mb-3" style="white-space: pre-wrap;">{{ $peluqueria->msj_bienvenida }}</div>
            <p><strong>NIT:</strong> {{ $peluqueria->nit }}</p>
            <p><strong>Dirección:</strong> {{ $peluqueria->direccion }}</p>
            <p><strong>Color del menú:</strong>
                @if($peluqueria->menu_color)
                    <span class="badge" style="background-color: {{ $peluqueria->menu_color }}; color: #fff;">
                        {{ $peluqueria->menu_color }}
                    </span>
                @else
                    <span class="text-muted">No configurado</span>
                @endif
            </p>
            <p><strong>Color del topbar:</strong>
                @if($peluqueria->topbar_color)
                    <span class="badge" style="background-color: {{ $peluqueria->topbar_color }}; color: #fff;">
                        {{ $peluqueria->topbar_color }}
                    </span>
                @else
                    <span class="text-muted">No configurado</span>
                @endif
            </p>
        </div>
    </div>

    <a href="{{ route('peluquerias.edit') }}" class="btn btn-primary">
        Editar perfil
    </a>
</div>
@endsection

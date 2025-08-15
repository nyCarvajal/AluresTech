@extends('layouts.vertical', ['subtitle' => 'Club'])



@section('content')
<div class="container">
    <h1>Perfil del Club</h1>

    <div class="card mb-4">
        <div class="card-body">
            <p><strong>Nombre:</strong> {{ $club->nombre }}</p>
            <p><strong>POS:</strong> {{ $club->pos ? 'Sí' : 'No' }}</p>
            <p><strong>Cuenta de Cobro:</strong> {{ $club->cuentaCobro ? 'Sí' : 'No' }}</p>
            <p><strong>Facturación Electrónica:</strong> {{ $club->electronica ? 'Sí' : 'No' }}</p>
            <p><strong>Términos:</strong></p>
            <div class="border p-2 mb-3" style="white-space: pre-wrap;">{{ $club->terminos }}</div>
           
            <p><strong>Mensaje Reserva Confirmada:</strong></p>
            <div class="border p-2 mb-3" style="white-space: pre-wrap;">{{ $club->msj_reserva_confirmada }}</div>
            <p><strong>Mensaje de Bienvenida:</strong></p>
            <div class="border p-2 mb-3" style="white-space: pre-wrap;">{{ $club->msj_bienvenida }}</div>
			 <p><strong>Mensaje de Recordatorio:</strong></p>
            <div class="border p-2 mb-3" style="white-space: pre-wrap;">{{ $club->msj_finalizado }}</div>
            <p><strong>Mensaje de paquete finjalizado:</strong></p>
            <div class="border p-2 mb-3" style="white-space: pre-wrap;">{{ $club->msj_bienvenida }}</div>
            <p><strong>NIT:</strong> {{ $club->nit }}</p>
            <p><strong>Dirección:</strong> {{ $club->direccion }}</p>
        </div>
    </div>

    <a href="{{ route('clubes.edit') }}" class="btn btn-primary">
        Editar perfil
    </a>
</div>
@endsection

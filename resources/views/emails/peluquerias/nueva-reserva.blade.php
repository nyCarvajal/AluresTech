@component('mail::message')
# Nueva Reserva registrada

Hola {{ $peluqueria->nombre }},

Se ha registrado una nueva solicitud de cita desde la página pública.

- **Cliente:** {{ trim($cliente->nombres . ' ' . ($cliente->apellidos ?? '')) }}
- **Correo:** {{ $cliente->correo }}
- **Fecha y hora:** {{ \Carbon\Carbon::parse($reserva->fecha)->format('d/m/Y H:i') }}
- **Duración:** {{ $reserva->duracion }} minutos
- **Tipo:** {{ $reserva->tipo ?? 'Reserva' }}
@isset($reserva->nota_cliente)
- **Nota del cliente:** {{ $reserva->nota_cliente }}
@endisset

Puedes confirmar la reserva haciendo clic en el siguiente botón para abrir AluresTech y aprobarla.

@php
    $confirmUrl = route('reservas.pending', ['reserva' => $reserva->id]);
@endphp

@component('mail::button', ['url' => $confirmUrl])
Confirmar reserva en AluresTech
@endcomponent

Gracias,
{{ config('app.name') }}
@endcomponent

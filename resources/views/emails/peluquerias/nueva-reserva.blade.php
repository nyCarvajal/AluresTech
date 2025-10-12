@component('mail::message')
# Nueva solicitud de reserva

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

@component('mail::button', ['url' => route('reservas.pending')])
Revisar solicitudes
@endcomponent

Gracias,
{{ config('app.name') }}
@endcomponent

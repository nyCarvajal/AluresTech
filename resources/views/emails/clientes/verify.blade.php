@component('mail::message')
# ¡Hola {{ $cliente->nombres }}!

Gracias por registrarte en **{{ $peluqueria->nombre }}**.

Para finalizar tu registro y poder agendar tus citas, por favor confirma tu correo electrónico haciendo clic en el siguiente botón:

@component('mail::button', ['url' => $verificationUrl])
Confirmar correo
@endcomponent

Si el botón no funciona, copia y pega este enlace en tu navegador:
{{ $verificationUrl }}

Gracias,<br>
El equipo de {{ $peluqueria->nombre }}
@endcomponent

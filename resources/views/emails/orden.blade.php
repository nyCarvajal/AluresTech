<!-- resources/views/emails/orden.blade.php -->
<!doctype html>
<html lang="es">
<head><meta charset="utf-8"></head>
<body>
    <p>Hola,</p>
    <p>Te compartimos la Orden de Compra #{{ $orden->id }} en formato PDF adjunto.</p>

    @if(!empty($mensaje))
        <p>{{ $mensaje }}</p>
    @endif

    <p>Saludos,</p>
    <p>{{ config('app.name') }}</p>
</body>
</html>

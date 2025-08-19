<!-- resources/views/ordenes/pdf.blade.php -->
@php
    $fmt = fn($v) => number_format($v, 0, ',', '.');
    $tz = 'America/Bogota';
    $fh = $orden->fecha_hora ? \Carbon\Carbon::parse($orden->fecha_hora)->timezone($tz)->format('d/m/Y H:i') : '—';
    $subtotal = optional($orden->ventas)->sum('valor_total') ?? 0;
    $pagado   = optional($orden->pagos)->sum('valor') ?? 0;
    $saldo    = max(0, $subtotal - $pagado);
@endphp
<!doctype html>
<html lang="es">
<head>
<meta charset="utf-8">
<title>Orden #{{ $orden->id }}</title>
<style>
    body { font-family: DejaVu Sans, sans-serif; font-size: 12px; color:#333; }
    .w-100{width:100%} .mb-2{margin-bottom:8px} .mb-3{margin-bottom:12px} .mb-4{margin-bottom:18px}
    .fw-bold{font-weight:bold} .text-end{text-align:right} .text-center{text-align:center}
    table{border-collapse:collapse; width:100%}
    th,td{border:1px solid #ddd; padding:6px}
    th{background:#f5f5f5}
    .small{font-size:11px}
</style>
</head>
<body>
    <h2 class="mb-2">Orden de Compra #{{ $orden->id }}</h2>
    <table class="mb-3">
        <tr>
            <td><span class="fw-bold">Fecha y hora:</span> {{ $fh }}</td>
            <td><span class="fw-bold">Responsable:</span> {{ $orden->responsable ?? '—' }}</td>
            <td><span class="fw-bold">Activa:</span> {{ $orden->activa ? 'Sí' : 'No' }}</td>
        </tr>
        <tr>
            <td><span class="fw-bold">Creado:</span> {{ optional($orden->created_at)->timezone($tz)->format('d/m/Y H:i') }}</td>
            <td><span class="fw-bold">Actualizado:</span> {{ optional($orden->updated_at)->timezone($tz)->format('d/m/Y H:i') }}</td>
            <td></td>
        </tr>
    </table>

    <h3 class="mb-2">Cliente</h3>
    <table class="mb-3">
        @if(is_array($orden->cliente) || is_object($orden->cliente))
        <tr>
            <td><span class="fw-bold">ID:</span> {{ data_get($orden->cliente, 'numero_identificacion', '—') }}</td>
            <td><span class="fw-bold">Nombre:</span> {{ trim((data_get($orden->cliente, 'nombres', '')) . ' ' . (data_get($orden->cliente, 'apellidos', ''))) ?: '—' }}</td>
            <td><span class="fw-bold">Correo:</span> {{ data_get($orden->cliente, 'correo', '—') }}</td>
        </tr>
        <tr>
            <td colspan="3"><span class="fw-bold">WhatsApp:</span> {{ data_get($orden->cliente, 'whatsapp', '—') }}</td>
        </tr>
        @else
        <tr><td colspan="3">{{ $orden->cliente ?: '—' }}</td></tr>
        @endif
    </table>

    <h3 class="mb-2">Ventas</h3>
    <table class="mb-3">
        <thead>
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
            @forelse($orden->ventas as $v)
                <tr>
                    <td>{{ $v->id }}</td>
                    <td>{{ optional($v->item)->nombre ?? '—' }}</td>
                    <td class="text-end">{{ $v->cantidad }}</td>
                    <td class="text-end">{{ rtrim(rtrim(number_format($v->descuento ?? 0, 2, ',', '.'), '0'), ',') }}%</td>
                    <td class="text-end">COP {{ $fmt($v->valor_unitario) }}</td>
                    <td class="text-end">COP {{ $fmt($v->valor_total) }}</td>
                </tr>
            @empty
                <tr><td colspan="6" class="text-center small">No hay ventas asociadas.</td></tr>
            @endforelse
        </tbody>
        <tfoot>
            <tr>
                <th colspan="5" class="text-end">Subtotal</th>
                <th class="text-end">COP {{ $fmt($subtotal) }}</th>
            </tr>
        </tfoot>
    </table>

    <h3 class="mb-2">Pagos</h3>
    <table class="mb-4">
        <thead>
            <tr>
                <th>ID</th>
                <th>Fecha</th>
                <th>Método</th>
                <th class="text-end">Valor</th>
            </tr>
        </thead>
        <tbody>
            @forelse($orden->pagos as $p)
                <tr>
                    <td>{{ $p->id }}</td>
                    <td>{{ optional($p->created_at)->timezone($tz)->format('d/m/Y H:i') }}</td>
                    <td>{{ ucfirst($p->metodo) }}</td>
                    <td class="text-end">COP {{ $fmt($p->valor) }}</td>
                </tr>
            @empty
                <tr><td colspan="4" class="text-center small">No hay pagos registrados.</td></tr>
            @endforelse
        </tbody>
        <tfoot>
            <tr>
                <th colspan="3" class="text-end">Total pagado</th>
                <th class="text-end">COP {{ $fmt($pagado) }}</th>
            </tr>
            <tr>
                <th colspan="3" class="text-end">Saldo</th>
                <th class="text-end">COP {{ $fmt($saldo) }}</th>
            </tr>
        </tfoot>
    </table>

    <div class="small">Generado el {{ now($tz)->format('d/m/Y H:i') }}</div>
</body>
</html>

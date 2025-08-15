@extends('layouts.vertical', ['subtitle' => 'Informe de Caja'])

@section('content')
<div class="container-fluid">
    <div class="row">
        <div class="col-12">

            {{-- Resumen total ------------------------------------------------ --}}
            <div class="card mb-4">
                <div class="card-body text-center">
                    <h3>Total en Caja:
                        {{ number_format($caja?->total() ?? 0, 2, ',', '.') }}
                    </h3>

                    <div class="table-responsive mt-3">
                        <table class="table table-bordered w-auto mx-auto">
                            <tr>
                                <th>Base</th>
                                <td>{{ number_format($caja?->base() ?? 0, 2, ',', '.') }}</td>
                            </tr>
                            <tr>
                                <th>Pagos en efectivo</th>
                                <td>{{ number_format($caja?->pagosEfectivo() ?? 0, 2, ',', '.') }}</td>
                            </tr>
                            <tr>
                                <th>Banco / Tarjeta</th>
                                <td>{{ number_format($caja?->pagosBanco() ?? 0, 2, ',', '.') }}</td>
                            </tr>
                            <tr>
                                <th>Total de pagos</th>
                                <td>{{ number_format($caja?->totalEntradas() ?? 0, 2, ',', '.') }}</td>
                            </tr>
                        </table>
                    </div>
                </div>
            </div>

            {{-- Salidas ------------------------------------------------------ --}}
            <x-caja-seccion titulo="Salidas">
                <x-slot:tabla>
                    <thead>
                        <tr>
                            <th>Concepto</th>
                            <th>Fecha</th>
                            <th>Valor</th>
                            <th>Observación</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($salidas as $s)
                            <tr>
                                <td>{{ $s->concepto }}</td>
                                <td>{{ $s->fecha->format('d/m/Y H:i') }}</td>
                                <td>{{ number_format($s->valor, 2, ',', '.') }}</td>
                                <td>{{ $s->observacion }}</td>
                            </tr>
                        @endforeach
                    </tbody>
                    <tfoot>
                        <tr>
                            <td colspan="2" class="text-end"><b>Total</b></td>
                            <td colspan="2" class="text-center">
                                {{ number_format($caja?->totalSalidas() ?? 0, 2, ',', '.') }}
                            </td>
                        </tr>
                    </tfoot>
                </x-slot:tabla>
            </x-caja-seccion>

            

            {{-- Pagos -------------------------------------------------------- --}}
            <x-caja-seccion titulo="Total de Pagos">
                <x-slot:tabla>
                    <thead>
                        <tr>
                            <th>Fecha</th>
                            <th># Orden</th>
                            <th>Forma de pago</th>
                            <th>Total</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($pagos as $p)
                            <tr>
                                <td>{{ $p->fecha->format('d/m/Y H:i') }}</td>
                                <td>
                                    <a href="{{ route('venta.show', $p->orden_id) }}">{{ $p->orden_id }}</a>
                                </td>
                                <td>{{ $p->metodo_pago }}</td>
                                <td>{{ number_format($p->valor, 2, ',', '.') }}</td>
                            </tr>
                        @endforeach
                    </tbody>
                    <tfoot>
                        <tr>
                            <td colspan="3" class="text-end"><b>Total</b></td>
                            <td class="text-center">
                                {{ number_format($caja?->totalFacturacion() ?? 0, 2, ',', '.') }}
                            </td>
                        </tr>
                    </tfoot>
                </x-slot:tabla>
</x-caja-seccion> 
        </div>
    </div>
</div>
@endsection

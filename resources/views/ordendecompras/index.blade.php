@extends('layouts.vertical')

@section('content')
<div class="container">
    <h1>Órdenes de Compra</h1>

    @if(session('success'))
        <div class="alert alert-success">{{ session('success') }}</div>
    @endif

    <style>
        .btn-orden-primary {
            background-color: #6f42c1;
            border-color: #6f42c1;
            color: #ffffff;
        }

        .btn-orden-primary:hover,
        .btn-orden-primary:focus {
            background-color: #5a32a3;
            border-color: #5a32a3;
            color: #ffffff;
        }

        .btn-orden-secondary {
            background-color: #f8f5ff;
            border-color: #6f42c1;
            color: #5a32a3;
        }

        .btn-orden-secondary:hover,
        .btn-orden-secondary:focus {
            background-color: #e8dfff;
            border-color: #5a32a3;
            color: #3f2475;
        }
    </style>


    <table class="table table-bordered">
        <thead>
            <tr>
                <th>ID</th>
                <th>Fecha y Hora</th>
                <th>Cliente</th>
                <th class="text-end">Total</th>
                <th class="text-end">Saldo pendiente</th>
                <th>Activa</th>
                <th>Acciones</th>
            </tr>
        </thead>
        <tbody>
            @foreach($ordenes as $orden)
                @php
                    $total = (float) ($orden->total_ventas ?? 0);
                    $pagado = (float) ($orden->total_pagado ?? 0);
                    $saldo = max($total - $pagado, 0);
                    $saldoClass = $saldo > 0 ? 'text-warning fw-semibold' : 'text-success fw-semibold';
                @endphp
                <tr>
                    <td>{{ $orden->id }}</td>
                    <td>{{ $orden->fecha_hora}}</td>
                    <td>
                        @if($orden->clienterel)
                            {{ $orden->clienterel->nombres }} {{ $orden->clienterel->apellidos }}
                        @else
                            <span class="text-muted">Sin cliente</span>
                        @endif
                    </td>
                    <td class="text-end">COP {{ number_format($total, 0, ',', '.') }}</td>
                    <td class="text-end">
                        <span class="{{ $saldoClass }}">COP {{ number_format($saldo, 0, ',', '.') }}</span>
                    </td>
                    <td>
                        @if($orden->activa)
                            <span class="badge bg-success">Sí</span>
                        @else
                            <span class="badge bg-secondary">No</span>
                        @endif
                    </td>
                    <td>
                        <div class="d-flex flex-wrap gap-2">
                            <a href="{{ route('orden_de_compras.show', $orden) }}" class="btn btn-sm btn-orden-secondary">Ver</a>
                            <a href="{{ route('ventas.index', ['orden_id' => $orden->id, 'cliente_id' => optional($orden->clienterel)->id ?? $orden->cliente]) }}" class="btn btn-sm btn-orden-primary">Gestionar</a>
                            <form action="{{ route('orden_de_compras.destroy', $orden) }}" method="POST" class="d-inline">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="btn btn-sm btn-outline-danger"
                                    onclick="return confirm('¿Eliminar esta orden de compra?')">Eliminar</button>
                            </form>
                        </div>
                    </td>
                </tr>
            @endforeach
        </tbody>
    </table>

    {{ $ordenes->links('vendor.pagination.bootstrap-5-sm') }}
</div>
@endsection

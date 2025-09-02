@extends('layouts.vertical', ['subtitle' => 'Ver Item'])


@section('content')
<div class="container">
    <h1 class="mb-4">Detalle del Ítem #{{ $item->id }}</h1>


    <div class="mb-3">

        @if($item->tipo == 1)
            <a href="{{ route('items.add-units-form', $item) }}" class="btn" style="background-color:#6f42c1; color:white;">Agregar unidades</a>
        @endif
        <a href="{{ route('items.edit', $item) }}" class="btn" style="background-color:#6f42c1; color:white;">Editar</a>
        <a href="{{ route('items.index') }}" class="btn btn-secondary">Volver al listado</a>
 </div>

    <div class="row">
        <div class="col-md-6">
            <div class="card mb-4" style="background-color:#f8f9fa; border-color:#6f42c1;">
                <div class="card-body">
                    <p><strong>Nombre:</strong> {{ $item->nombre }}</p>
                    @if($item->tipo == 1)
                        <p><strong>Costo:</strong> {{ number_format($item->costo, 2, ',', '.') }}</p>
                        <p><strong>Cantidad:</strong> {{ $item->cantidad }}</p>
                    @endif
                    <p><strong>Valor:</strong> {{ number_format($item->valor, 2, ',', '.') }}</p>
                    <p><strong>Tipo:</strong> {{ $item->tipo == 1 ? 'Producto' : 'Servicio' }}</p>
                    <p><strong>Área:</strong> {{ $item->area }}</p>
                    <p><strong>Creado:</strong> {{ $item->created_at->format('d/m/Y H:i') }}</p>
                    <p><strong>Última actualización:</strong> {{ $item->updated_at->format('d/m/Y H:i') }}</p>
                </div>
            </div>
        </div>
        @if($item->tipo == 1)
        <div class="col-md-6">
            <div class="card mb-4" style="background-color:#f8f9fa; border-color:#6f42c1;">
                <div class="card-body">
                    <h2 class="mb-3" style="color:#6f42c1;">Historial de inventario</h2>
                    @if($item->movimientos->count())
                    <table class="table">
                        <thead>
                            <tr>
                                <th>Fecha</th>
                                <th>Cambio</th>
                                <th>Descripción</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($item->movimientos as $mov)
                                <tr>
                                    <td>{{ $mov->created_at->format('d/m/Y H:i') }}</td>
                                    <td>{{ $mov->cambio }}</td>
                                    <td>{{ $mov->descripcion }}</td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                    @else
                        <p>No hay movimientos de inventario.</p>
                    @endif
                </div>
            </div>
        </div>
        @endif
    </div>

</div>
@endsection

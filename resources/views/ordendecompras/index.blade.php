@extends('layouts.vertical')

@section('content')
<div class="container">
    <h1>Órdenes de Compra</h1>

    @if(session('success'))
        <div class="alert alert-success">{{ session('success') }}</div>
    @endif

   
    <table class="table table-bordered">
        <thead>
            <tr>
                <th>ID</th>
                <th>Fecha y Hora</th>
                <th>Cliente</th>
                <th>Activa</th>
                <th>Acciones</th>
            </tr>
        </thead>
        <tbody>
            @foreach($ordenes as $orden)
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
                    <td>
                        @if($orden->activa)
                            <span class="badge bg-success">Sí</span>
                        @else
                            <span class="badge bg-secondary">No</span>
                        @endif
                    </td>
                    <td>
                        <a href="{{ route('orden_de_compras.show', $orden) }}" class="btn btn-sm btn-info">Ver</a>
                        <a href="{{ route('orden_de_compras.edit', $orden) }}" class="btn btn-sm btn-warning">Editar</a>
                        <form action="{{ route('orden_de_compras.destroy', $orden) }}" method="POST" style="display:inline">
                            @csrf
                            @method('DELETE')
                            <button type="submit" class="btn btn-sm btn-danger"
                                onclick="return confirm('¿Eliminar esta orden de compra?')">Eliminar</button>
                        </form>
                    </td>
                </tr>
            @endforeach
        </tbody>
    </table>

    {{ $ordenes->links('vendor.pagination.bootstrap-5-sm') }}
</div>
@endsection

@extends('layouts.vertical')

@section('content')
<div class="container">
    <h1 class="mb-4">Listado de Ítems</h1>

    @if (session('success'))
        <div class="alert alert-success">
            {{ session('success') }}
        </div>
    @endif

    <div class="d-flex justify-content-between mb-3">
        <form method="GET" action="{{ route('items.index') }}" class="d-flex w-50">
            <input type="text" name="search" class="form-control me-2" placeholder="Buscar..." value="{{ request('search') }}">
            <button type="submit" class="btn btn-secondary">Buscar</button>
        </form>
        <a href="{{ route('items.create') }}" class="btn btn-primary">Nuevo Ítem</a>
    </div>

    @if ($items->count())
        <table class="table table-striped">
            <thead>
                <tr>
                    <th>ID</th>
                    <th>Nombre</th>
                    <th>Valor</th>
                    <th>Tipo</th>
                    <th>Área</th>
                    <th>Cantidad</th>
                    <th>Costo</th>
                    <th>Acciones</th>
                </tr>
            </thead>
            <tbody>
                @foreach ($items as $item)
                    <tr>
                        <td>{{ $item->id }}</td>
                        <td>{{ $item->nombre }}</td>
                        <td>{{ number_format($item->valor, 2, ',', '.') }}</td>
                        <td>{{ $item->tipo == 1 ? 'Producto' : 'Servicio' }}</td>
                        <td>{{ $item->areaRelation?->descripcion ?? 'Sin asignar' }}</td>
                        <td>{{ $item->tipo == 1 ? $item->cantidad : '-' }}</td>
                        <td>{{ $item->tipo == 1 ? number_format($item->costo, 2, ',', '.') : '-' }}</td>

                        <td>
                            <a href="{{ route('items.show', $item) }}" class="btn btn-sm btn-secondary">Ver</a>
                            <a href="{{ route('items.edit', $item) }}" class="btn btn-sm btn-primary">Editar</a>

                            <form action="{{ route('items.destroy', $item) }}" method="POST" class="d-inline" onsubmit="return confirm('¿Eliminar este ítem?');">
                                @csrf
                                @method('DELETE')
                                <button class="btn btn-sm btn-secondary" type="submit">Eliminar</button>
                            </form>
                        </td>
                    </tr>
                @endforeach
            </tbody>
        </table>

        {{-- Paginación --}}
        {{ $items->links('pagination::bootstrap-5') }}
    @else
        <p>No hay ítems registrados.</p>
    @endif
</div>
@endsection

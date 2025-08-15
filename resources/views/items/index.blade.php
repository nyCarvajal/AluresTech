@extends('layouts.vertical')

@section('content')
<div class="container">
    <h1 class="mb-4">Listado de Servicioss</h1>

    @if (session('success'))
        <div class="alert alert-success">
            {{ session('success') }}
        </div>
    @endif

    <a href="{{ route('items.create') }}" class="btn btn-primary mb-3">Nuevo Servicio</a>

    @if ($items->count())
        <table class="table table-striped">
            <thead>
                <tr>
                    <th>ID</th>
                    <th>Nombre</th>
                    <th>Valor</th>
                    <th>Acciones</th>
                </tr>
            </thead>
            <tbody>
                @foreach ($items as $item)
                    <tr>
                        <td>{{ $item->id }}</td>
                        <td>{{ $item->nombre }}</td>
                        <td>{{ number_format($item->valor, 2, ',', '.') }}</td>
                       
                        <td>
                            <a href="{{ route('items.show', $item) }}" class="btn btn-sm btn-secondary">Ver</a>
                            <a href="{{ route('items.edit', $item) }}" class="btn btn-sm btn-warning">Editar</a>

                            <form action="{{ route('items.destroy', $item) }}" method="POST" class="d-inline"
                                  onsubmit="return confirm('¿Eliminar este ítem?');">
                                @csrf
                                @method('DELETE')
                                <button class="btn btn-sm btn-danger" type="submit">Eliminar</button>
                            </form>
                        </td>
                    </tr>
                @endforeach
            </tbody>
        </table>

        {{-- Paginación --}}
        {{ $items->links() }}
    @else
        <p>No hay ítems registrados.</p>
    @endif
</div>
@endsection

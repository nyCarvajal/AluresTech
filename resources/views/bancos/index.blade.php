@extends('layouts.vertical', ['subtitle' => 'Inicio'])


@section('content')
<div class="container">
    <h1 class="mb-4">Listado de Bancos</h1>

    @if (session('success'))
        <div class="alert alert-success">
            {{ session('success') }}
        </div>
    @endif

    <a href="{{ route('bancos.create') }}" class="btn btn-primary mb-3">Crear Nuevo Banco</a>

    @if ($bancos->count())
        <table class="table table-striped">
            <thead>
                <tr>
                    <th>ID</th>
                    <th>Nombre</th>
                    <th>Saldo Inicial</th>
                    <th>Saldo Actual</th>
                    <th>Acciones</th>
                </tr>
            </thead>
            <tbody>
                @foreach ($bancos as $banco)
                    <tr>
                        <td>{{ $banco->id }}</td>
                        <td>{{ $banco->nombre }}</td>
                        <td>{{ number_format($banco->saldo_inicial, 2, ',', '.') }}</td>
                        <td>{{ number_format($banco->saldo_actual, 2, ',', '.') }}</td>
                        <td>
                            <a href="{{ route('bancos.show', $banco) }}" class="btn btn-sm btn-secondary">Ver</a>
                            <a href="{{ route('bancos.edit', $banco) }}" class="btn btn-sm btn-warning">Editar</a>

                            <form action="{{ route('bancos.destroy', $banco) }}" method="POST" class="d-inline"
                                  onsubmit="return confirm('¿Seguro que deseas eliminar este banco?');">
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
        {{ $bancos->links() }}
    @else
        <p>No hay bancos registrados.</p>
    @endif
</div>
@endsection

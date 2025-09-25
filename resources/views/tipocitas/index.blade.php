@extends('layouts.vertical', ['subtitle' => 'Tipos de cita'])

@section('content')
<div class="container">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h1 class="h3 mb-0">Tipos de cita</h1>
        <a href="{{ route('tipocitas.create') }}" class="btn btn-primary">Nuevo tipo de cita</a>
    </div>

    @if(session('success'))
        <div class="alert alert-success">{{ session('success') }}</div>
    @endif

    <div class="card">
        <div class="table-responsive">
            <table class="table table-striped mb-0">
                <thead>
                    <tr>
                        <th scope="col">ID</th>
                        <th scope="col">Nombre</th>
                        <th scope="col" class="text-end">Acciones</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($tipocitas as $tipocita)
                        <tr>
                            <td>{{ $tipocita->id }}</td>
                            <td>{{ $tipocita->nombre }}</td>
                            <td class="text-end">
                                <a href="{{ route('tipocitas.edit', $tipocita) }}" class="btn btn-sm btn-secondary">Editar</a>
                                <form action="{{ route('tipocitas.destroy', $tipocita) }}" method="POST" class="d-inline">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="btn btn-sm btn-danger" onclick="return confirm('¿Eliminar este tipo de cita?')">
                                        Eliminar
                                    </button>
                                </form>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="3" class="text-center">No hay tipos de cita registrados.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        <div class="card-footer">
            {{ $tipocitas->links() }}
        </div>
    </div>
</div>
@endsection

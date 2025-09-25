@extends('layouts.vertical', ['subtitle' => 'Tipos de identificación'])

@section('content')
<div class="container">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h1 class="h3 mb-0">Tipos de identificación</h1>
        <a href="{{ route('tipo-identificaciones.create') }}" class="btn btn-primary">Nuevo tipo</a>
    </div>

    @if (session('success'))
        <div class="alert alert-success">{{ session('success') }}</div>
    @endif

    <div class="card">
        <div class="card-body p-0">
            <div class="table-responsive">
                <table class="table table-striped mb-0">
                    <thead>
                        <tr>
                            <th class="text-center" style="width: 80px;">ID</th>
                            <th>Tipo</th>
                            <th class="text-end" style="width: 200px;">Acciones</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse ($tiposIdentificacion as $tipo)
                            <tr>
                                <td class="text-center">{{ $tipo->id }}</td>
                                <td>{{ $tipo->tipo }}</td>
                                <td class="text-end">
                                    <a href="{{ route('tipo-identificaciones.edit', $tipo) }}" class="btn btn-sm btn-primary">Editar</a>
                                    <form action="{{ route('tipo-identificaciones.destroy', $tipo) }}" method="POST" class="d-inline" onsubmit="return confirm('¿Eliminar este tipo?');">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="btn btn-sm btn-outline-danger">Eliminar</button>
                                    </form>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="3" class="text-center py-4">No hay tipos de identificación registrados.</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
        @if ($tiposIdentificacion->hasPages())
            <div class="card-footer">
                {{ $tiposIdentificacion->links('pagination::bootstrap-5') }}
            </div>
        @endif
    </div>
</div>
@endsection

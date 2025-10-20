@extends('layouts.vertical', ['subtitle' => 'Listado de Áreas'])

@section('content')
<div class="container">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h1 class="h3 mb-0">Áreas</h1>
        <a href="{{ route('areas.create') }}" class="btn btn-primary">Nueva Área</a>
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
                            <th class="text-center" style="width: 80px;">#</th>
                            <th>Descripción</th>
                            <th class="text-end" style="width: 160px;">Acciones</th>
                        </tr>
                    </thead>
                    <tbody>
                        @php
                            $startIndex = $areas->firstItem() ?? 1;
                        @endphp
                        @forelse ($areas as $area)
                            <tr>
                                <td class="text-center">{{ $startIndex + $loop->index }}</td>
                                <td>{{ $area->descripcion }}</td>
                                <td class="text-end">
                                    <a href="{{ route('areas.edit', $area) }}" class="btn btn-sm btn-primary">Editar</a>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="3" class="text-center py-4">No hay áreas registradas.</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
        @if ($areas->hasPages())
            <div class="card-footer">
                {{ $areas->links('pagination::bootstrap-5') }}
            </div>
        @endif
    </div>
</div>
@endsection

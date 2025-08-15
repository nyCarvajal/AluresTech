@extends('layouts.vertical', ['subtitle' => 'Cajas'])

@section('content')
<div class="container-fluid">
    <div class="row">
        <div class="col-12">

            {{-- Encabezado + botón --}}
            <div class="d-flex justify-content-between align-items-center mb-3">
                <h3 class="mb-0">Listado de Cajas</h3>
                <a href="{{ route('cajas.create') }}" class="btn btn-primary">
                    Nueva Caja
                </a>
            </div>

            {{-- Tabla --}}
            <div class="card">
                <div class="card-body p-0">
                    <div class="table-responsive">
                        <table class="table table-striped align-middle mb-0">
                            <thead class="table-light">
                                <tr>
                                    <th>#</th>
                                    <th>Fecha y Hora</th>
                                    <th>Base (COP)</th>
                                    <th>Valor Retirado (COP)</th>
                                    <th class="text-center">Acciones</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($cajas as $caja)
                                    <tr>
                                        <td>{{ $loop->iteration }}</td>
                                        <td>{{ $caja->fecha_hora->format('d/m/Y H:i') }}</td>
                                        <td>{{ number_format($caja->base, 2, ',', '.') }}</td>
                                        <td>{{ number_format($caja->valor ?? 0, 2, ',', '.') }}</td>
                                        <td class="text-center">
                                            <a href="{{ route('cajas.show', $caja) }}"
                                               class="btn btn-sm btn-info">Ver</a>
                                            <a href="{{ route('cajas.edit', $caja) }}"
                                               class="btn btn-sm btn-success">Editar</a>
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="5" class="text-center">Sin registros</td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div><!-- /.card-body -->
            </div><!-- /.card -->

        </div>
    </div>
</div>
@endsection

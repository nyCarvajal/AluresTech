@extends('layouts.vertical', ['subtitle' => 'Salidas'])

@section('content')
<div class="container-fluid">
    <div class="row">
        <div class="col-12">

            {{-- Card ---------------------------------------------------------- --}}
            <div class="card">
                {{-- Encabezado: título + botón --}}
                <div class="card-header d-flex justify-content-between align-items-center">
                    <h5 class="mb-0">Salidas</h5>

                    <a href="{{ route('salidas.create') }}" class="btn btn-primary">
                        Nueva Salida
                    </a>
                </div>

                {{-- Cuerpo --}}
                <div class="card-body">
                    {{-- Flash de éxito --}}
                    @if(session('success'))
                        <div class="alert alert-success">{{ session('success') }}</div>
                    @endif

                    {{-- Tabla responsive para evitar que empuje el menú lateral --}}
                    <div class="table-responsive">
                        <table class="table table-striped align-middle mb-0">
                            <thead class="table-light">
                                <tr>
                                    <th>ID</th>
                                    <th>Concepto</th>
                                    <th>Fecha</th>
                                    <th>Origen</th>
                                    <th>Valor</th>
                                    <th>Responsable</th>
                                    <th>Tercero</th>
                                    <th class="text-center">Acciones</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($salidas as $s)
                                    <tr>
                                        <td>{{ $s->id }}</td>
                                        <td>{{ $s->concepto }}</td>
                                        <td>{{ optional($s->fecha)->format('Y-m-d') }}</td>
                                        <td>
                                            {{ $s->cuenta_bancaria_id ? ($s->cuentaBancaria->nombre ?? 'Cuenta bancaria') : 'Caja' }}
                                        </td>

                                        <td>{{ number_format($s->valor, 0, ',', '.') }}</td>
                                        <td>{{ optional($s->responsable)->nombre }}</td>

                                        <td>{{ optional($s->tercero)->nombre }}</td>

                                        <td class="text-center">
                                            <div class="btn-group btn-group-sm" role="group">
                                                <a href="{{ route('salidas.show', $s) }}"
                                                   class="btn btn-info">Ver</a>

                                                <a href="{{ route('salidas.edit', $s) }}"
                                                   class="btn btn-secondary">Editar</a>

                                                <form action="{{ route('salidas.destroy', $s) }}"
                                                      method="POST"
                                                      onsubmit="return confirm('¿Eliminar esta salida?');">
                                                    @csrf
                                                    @method('DELETE')
                                                    <button class="btn btn-danger">Eliminar</button>
                                                </form>
                                            </div>
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div> {{-- /.table-responsive --}}
                </div> {{-- /.card-body --}}
            </div> {{-- /.card --}}

        </div>
    </div>
</div>
@endsection

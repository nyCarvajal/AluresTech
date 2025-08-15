@extends('layouts.vertical', ['subtitle' => 'Editar Caja'])

@section('content')
<div class="container-fluid">
    <div class="row">
        <div class="col-lg-8 col-xl-6 mx-auto">

            <div class="card">
                <div class="card-header">
                    <h4 class="mb-0">Editar Caja #{{ $caja->id }}</h4>
                </div>

                <form action="{{ route('cajas.update', $caja) }}" method="POST">
                    @csrf @method('PUT')
                    <div class="card-body">

                        {{-- Fecha y hora --}}
                        <div class="mb-3">
                            <label for="fecha_hora" class="form-label">Fecha y Hora</label>
                            <input type="datetime-local"
                                   id="fecha_hora"
                                   name="fecha_hora"
                                   class="form-control @error('fecha_hora') is-invalid @enderror"
                                   value="{{ old('fecha_hora', $caja->fecha_hora->format('Y-m-d\TH:i')) }}"
                                   required>
                            @error('fecha_hora')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        {{-- Base --}}
                        <div class="mb-3">
                            <label for="base" class="form-label">Base (COP)</label>
                            <input type="number" step="0.01" min="0"
                                   id="base"
                                   name="base"
                                   class="form-control @error('base') is-invalid @enderror"
                                   value="{{ old('base', $caja->base) }}"
                                   required>
                            @error('base')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        {{-- Valor retirado --}}
                        <div class="mb-3">
                            <label for="valor" class="form-label">Valor Retirado (COP)</label>
                            <input type="number" step="0.01" min="0"
                                   id="valor"
                                   name="valor"
                                   class="form-control @error('valor') is-invalid @enderror"
                                   value="{{ old('valor', $caja->valor) }}">
                            @error('valor')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                    </div><!-- /.card-body -->

                    <div class="card-footer d-flex justify-content-between">
                        <a href="{{ route('cajas.index') }}" class="btn btn-outline-secondary">
                            Volver
                        </a>
                        <button type="submit" class="btn btn-primary">
                            Guardar Cambios
                        </button>
                    </div>
                </form>
            </div><!-- /.card -->

        </div>
    </div>
</div>
@endsection

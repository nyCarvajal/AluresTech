@extends('layouts.vertical', ['subtitle' => 'Abrir Caja'])

@section('content')
<div class="container-fluid">
    <div class="row">
        <div class="col-lg-8 col-xl-6 mx-auto">

            <div class="card">
                <div class="card-header">
                    <h4 class="mb-0">Nueva Caja</h4>
                </div>

                <form action="{{ route('cajas.store') }}" method="POST">
                    @csrf
                    <div class="card-body">

                        {{-- Fecha y hora (editable) --}}
                        <div class="mb-3">
                            <label for="fecha_hora" class="form-label">Fecha y Hora</label>
                            <input type="datetime-local"
                                   id="fecha_hora"
                                   name="fecha_hora"
                                   class="form-control @error('fecha_hora') is-invalid @enderror"
                                   value="{{ old('fecha_hora', now()->format('Y-m-d\TH:i')) }}"
                                   required>
                            @error('fecha_hora')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        {{-- Base inicial --}}
                        <div class="mb-3">
                            <label for="base" class="form-label">Base (COP)</label>
                            <input type="number" step="0.01" min="0"
                                   id="base"
                                   name="base"
                                   class="form-control @error('base') is-invalid @enderror"
                                   value="{{ old('base') }}"
                                   required>
                            @error('base')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        {{-- Valor retirado (opcional al crear) --}}
                        <div class="mb-3">
                            <label for="valor" class="form-label">Valor Retirado (COP)</label>
                            <input type="number" step="0.01" min="0"
                                   id="valor"
                                   name="valor"
                                   class="form-control @error('valor') is-invalid @enderror"
                                   value="{{ old('valor', 0) }}">
                            @error('valor')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                    </div><!-- /.card-body -->

                    <div class="card-footer d-flex justify-content-between">
                        <a href="{{ route('cajas.index') }}" class="btn btn-outline-secondary">
                            Cancelar
                        </a>
                        <button type="submit" class="btn btn-primary">
                            Guardar
                        </button>
                    </div>
                </form>
            </div><!-- /.card -->

        </div>
    </div>
</div>
@endsection

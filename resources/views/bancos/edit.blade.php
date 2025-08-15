@extends('layouts.vertical', ['subtitle' => 'Inicio'])

@section('content')
<div class="container">
    <h1 class="mb-4">Editar Banco #{{ $banco->id }}</h1>

    {{-- Mostrar errores de validación --}}
    @if ($errors->any())
        <div class="alert alert-danger">
            <ul class="mb-0">
                @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <form action="{{ route('bancos.update', $banco) }}" method="POST">
        @csrf
        @method('PUT')

        <div class="mb-3">
            <label for="nombre" class="form-label">Nombre</label>
            <input type="text" name="nombre" id="nombre"
                   class="form-control @error('nombre') is-invalid @enderror"
                   value="{{ old('nombre', $banco->nombre) }}" required>
            @error('nombre')
                <div class="invalid-feedback">{{ $message }}</div>
            @enderror
        </div>

        <div class="mb-3">
            <label for="saldo_inicial" class="form-label">Saldo Inicial</label>
            <input type="number" step="0.01" name="saldo_inicial" id="saldo_inicial"
                   class="form-control @error('saldo_inicial') is-invalid @enderror"
                   value="{{ old('saldo_inicial', $banco->saldo_inicial) }}" min="0" required>
            @error('saldo_inicial')
                <div class="invalid-feedback">{{ $message }}</div>
            @enderror
        </div>

        <div class="mb-3">
            <label for="saldo_actual" class="form-label">Saldo Actual</label>
            <input type="number" step="0.01" name="saldo_actual" id="saldo_actual"
                   class="form-control @error('saldo_actual') is-invalid @enderror"
                   value="{{ old('saldo_actual', $banco->saldo_actual) }}" min="0" required>
            @error('saldo_actual')
                <div class="invalid-feedback">{{ $message }}</div>
            @enderror
        </div>

        <button type="submit" class="btn btn-primary">Actualizar</button>
        <a href="{{ route('bancos.index') }}" class="btn btn-secondary">Cancelar</a>
    </form>
</div>
@endsection

@extends('layouts.vertical', ['subtitle' => 'Editar Item'])


@section('content')
<div class="container">
    <h1 class="mb-4">Editar Ítem #{{ $item->id }}</h1>

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

    <form action="{{ route('items.update', $item) }}" method="POST">
        @csrf
        @method('PUT')

        <div class="mb-3">
            <label for="nombre" class="form-label">Nombre</label>
            <input type="text" name="nombre" id="nombre"
                   class="form-control @error('nombre') is-invalid @enderror"
                   value="{{ old('nombre', $item->nombre) }}" required>
            @error('nombre')
                <div class="invalid-feedback">{{ $message }}</div>
            @enderror
        </div>

        <div class="mb-3">
            <label for="tipo" class="form-label">Tipo</label>
            <select name="tipo" id="tipo" class="form-control">
                <option value="0" {{ old('tipo', $item->tipo) == 0 ? 'selected' : '' }}>Servicio</option>
                <option value="1" {{ old('tipo', $item->tipo) == 1 ? 'selected' : '' }}>Producto</option>
            </select>
        </div>

        <div id="producto-fields" style="display:none;">
            <div class="mb-3">
                <label for="costo" class="form-label">Costo</label>
                <input type="number" step="0.01" name="costo" id="costo"
                       class="form-control @error('costo') is-invalid @enderror"
                       value="{{ old('costo', $item->costo) }}" min="0">
                @error('costo')
                    <div class="invalid-feedback">{{ $message }}</div>
                @enderror
            </div>

            <div class="mb-3">
                <label for="cantidad" class="form-label">Cantidad</label>
                <input type="number" name="cantidad" id="cantidad"
                       class="form-control @error('cantidad') is-invalid @enderror"
                       value="{{ old('cantidad', $item->cantidad) }}" min="0">
                @error('cantidad')
                    <div class="invalid-feedback">{{ $message }}</div>
                @enderror
            </div>
        </div>

        <div class="mb-3">
            <label for="valor" class="form-label">Valor</label>
            <input type="number" step="0.01" name="valor" id="valor"
                   class="form-control @error('valor') is-invalid @enderror"
                   value="{{ old('valor', $item->valor) }}" min="0" required>
            @error('valor')
                <div class="invalid-feedback">{{ $message }}</div>
            @enderror
        </div>

        <div class="mb-3">
            <label for="area" class="form-label">Área</label>
            <input type="text" name="area" id="area"
                   class="form-control @error('area') is-invalid @enderror"
                   value="{{ old('area', $item->area) }}">
            @error('area')
                <div class="invalid-feedback">{{ $message }}</div>
            @enderror
        </div>

        <button type="submit" class="btn btn-primary">Actualizar</button>
        <a href="{{ route('items.index') }}" class="btn btn-secondary">Cancelar</a>
    </form>
</div>
<script>
    document.addEventListener('DOMContentLoaded', function () {
        const tipo = document.getElementById('tipo');
        const fields = document.getElementById('producto-fields');
        function toggleFields() {
            fields.style.display = tipo.value === '1' ? 'block' : 'none';
        }
        tipo.addEventListener('change', toggleFields);
        toggleFields();
    });
</script>
@endsection

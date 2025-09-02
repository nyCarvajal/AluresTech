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

        <div class="row">
            <div class="mb-3 col-md-6">
                <label for="valor" class="form-label">Valor</label>
                <input type="text" name="valor" id="valor"
                       class="form-control currency-input @error('valor') is-invalid @enderror"
                       value="{{ old('valor', $item->valor) }}" required>
                @error('valor')
                    <div class="invalid-feedback">{{ $message }}</div>
                @enderror
            </div>
            <div class="mb-3 col-md-6" id="costo-field" style="display:none;">
                <label for="costo" class="form-label">Costo</label>
                <input type="text" name="costo" id="costo"
                       class="form-control currency-input @error('costo') is-invalid @enderror"
                       value="{{ old('costo', $item->costo) }}">
                @error('costo')
                    <div class="invalid-feedback">{{ $message }}</div>
                @enderror
            </div>
        </div>

        <div class="mb-3" id="cantidad-field" style="display:none;">
            <label for="cantidad" class="form-label">Cantidad</label>
            <input type="number" name="cantidad" id="cantidad"
                   class="form-control @error('cantidad') is-invalid @enderror"
                   value="{{ old('cantidad', $item->cantidad) }}" min="0">
            @error('cantidad')
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
 const costoField = document.getElementById('costo-field');
        const cantidadField = document.getElementById('cantidad-field');
        const currencyInputs = document.querySelectorAll('.currency-input');


        function toggleFields() {
            const isProduct = tipo.value === '1';
            costoField.style.display = isProduct ? 'block' : 'none';
            cantidadField.style.display = isProduct ? 'block' : 'none';

        }

        function formatCOP(value) {
            if (!value) return '';
            let n = parseFloat(value.toString()
                .replace(/[^0-9\.\,]/g, '')
                .replace(/,/g, '.'));
            if (isNaN(n)) return '';
            return n.toLocaleString('es-CO', { style: 'currency', currency: 'COP' });
        }

        function parseCOP(formatted) {
            if (!formatted) return 0;
            let plain = formatted
                .replace(/[^0-9\.\,]/g, '')
                .replace(/\./g, '')
                .replace(/,/g, '.');
            let n = parseFloat(plain);
            return isNaN(n) ? 0 : n;
        }

        currencyInputs.forEach(input => {
            input.value = formatCOP(input.value);
            input.addEventListener('blur', function () {
                this.value = formatCOP(this.value);
            });
            input.addEventListener('focus', function () {
                let num = parseCOP(this.value);
                this.value = num ? num.toFixed(2).replace('.', ',') : '';
            });
        });

        document.querySelector('form').addEventListener('submit', function () {
            currencyInputs.forEach(input => {
                input.value = parseCOP(input.value);
            });
        });


        tipo.addEventListener('change', toggleFields);
        toggleFields();
    });
</script>
@endsection

@php
    $editando = $salida->exists;
    $usuarioAutenticado = auth()->user();
    $origenSeleccionado = old('origen');

    if (!in_array($origenSeleccionado, ['caja', 'banco'], true)) {
        $origenSeleccionado = $editando && $salida->cuenta_bancaria_id ? 'banco' : 'caja';
    }
@endphp

{{-- Concepto --}}
<div class="mb-3">
    <label for="concepto" class="form-label">Concepto</label>
    <input
        type="text"
        id="concepto"
        name="concepto"
        value="{{ old('concepto', $editando ? $salida->concepto : '') }}"
        class="form-control"
        required
    >
</div>

{{-- Fecha --}}
<div class="mb-3">
    <label for="fecha" class="form-label">Fecha</label>
    <input
        type="date"
        id="fecha"
        name="fecha"
        value="{{ old('fecha', ($editando && $salida->fecha) ? $salida->fecha->format('Y-m-d') : now()->format('Y-m-d')) }}"
        class="form-control"
        required
    >
</div>

{{-- Tercero --}}
<div class="mb-3">
    <label for="tercero_id" class="form-label">Tercero (Proveedor)</label>
    <select
        id="tercero_id"
        name="tercero_id"
        class="form-select"
        required
    >
        <option value="" disabled {{ old('tercero_id', $editando ? $salida->tercero_id : '') === '' ? 'selected' : '' }}>
            — Selecciona proveedor —
        </option>
        @foreach($proveedores as $proveedor)
            <option
                value="{{ $proveedor->id }}"
                {{ (string) old('tercero_id', $editando ? $salida->tercero_id : '') === (string) $proveedor->id ? 'selected' : '' }}
            >
                {{ $proveedor->nombre }}
            </option>
        @endforeach
    </select>
</div>

{{-- Origen de los fondos --}}
<div class="mb-3">
    <label class="form-label d-block">Origen de los fondos</label>
    <div class="form-check form-check-inline">
        <input
            class="form-check-input"
            type="radio"
            name="origen"
            id="origen_caja"
            value="caja"
            {{ $origenSeleccionado === 'caja' ? 'checked' : '' }}
        >
        <label class="form-check-label" for="origen_caja">Caja</label>
    </div>
    <div class="form-check form-check-inline">
        <input
            class="form-check-input"
            type="radio"
            name="origen"
            id="origen_banco"
            value="banco"
            {{ $origenSeleccionado === 'banco' ? 'checked' : '' }}
        >
        <label class="form-check-label" for="origen_banco">Cuenta bancaria</label>
    </div>
</div>

{{-- Cuenta bancaria --}}
<div class="mb-3" id="contenedor-cuenta-bancaria">
    <label for="cuenta_bancaria_id" class="form-label">Cuenta bancaria</label>
    <select
        id="cuenta_bancaria_id"
        name="cuenta_bancaria_id"
        class="form-select"
        {{ $origenSeleccionado === 'caja' ? 'disabled' : '' }}
    >
        <option value="" disabled {{ old('cuenta_bancaria_id', $editando ? $salida->cuenta_bancaria_id : '') === '' ? 'selected' : '' }}>
            — Selecciona banco —
        </option>
        @foreach($bancos as $banco)
            <option
                value="{{ $banco->id }}"
                {{ (string) old('cuenta_bancaria_id', $editando ? $salida->cuenta_bancaria_id : '') === (string) $banco->id ? 'selected' : '' }}
            >
                {{ $banco->nombre }}
            </option>
        @endforeach
    </select>
</div>

{{-- Valor --}}
<div class="mb-3">
    <label for="valor" class="form-label">Valor</label>
    <input
        type="number"
        step="0.01"
        min="0"
        id="valor"
        name="valor"
        value="{{ old('valor', $editando ? $salida->valor : '') }}"
        class="form-control"
        required
    >
</div>

{{-- Observaciones --}}
<div class="mb-3">
    <label for="observaciones" class="form-label">Observaciones</label>
    <textarea
        id="observaciones"
        name="observaciones"
        class="form-control"
        rows="3"
    >{{ old('observaciones', $editando ? $salida->observaciones : '') }}</textarea>
</div>

{{-- Responsable --}}
<div class="mb-3">
    <label class="form-label">Responsable</label>
    @if($editando)
        <select name="responsable_id" class="form-select" required>
            <option value="" disabled {{ old('responsable_id', $salida->responsable_id) === null ? 'selected' : '' }}>
                — Selecciona usuario —
            </option>
            @foreach($usuarios as $usuario)
                <option
                    value="{{ $usuario->id }}"
                    {{ (string) old('responsable_id', $salida->responsable_id) === (string) $usuario->id ? 'selected' : '' }}
                >
                    {{ $usuario->name }}
                </option>
            @endforeach
        </select>
    @else
        <input
            type="text"
            class="form-control"
            value="{{ optional($usuarioAutenticado)->name }}"
            disabled
        >
        <input
            type="hidden"
            name="responsable_id"
            value="{{ old('responsable_id', optional($usuarioAutenticado)->id) }}"
        >
    @endif
</div>

@push('scripts')
<script>
    document.addEventListener('DOMContentLoaded', function () {
        const radiosOrigen = document.querySelectorAll('input[name="origen"]');
        const selectBanco = document.getElementById('cuenta_bancaria_id');
        const contenedorBanco = document.getElementById('contenedor-cuenta-bancaria');

        function toggleBanco() {
            const origenSeleccionado = document.querySelector('input[name="origen"]:checked');
            const mostrarBanco = origenSeleccionado && origenSeleccionado.value === 'banco';

            if (contenedorBanco) {
                contenedorBanco.classList.toggle('d-none', !mostrarBanco);
            }

            if (selectBanco) {
                selectBanco.disabled = !mostrarBanco;

                if (!mostrarBanco) {
                    selectBanco.value = '';
                }
            }
        }

        radiosOrigen.forEach(function (radio) {
            radio.addEventListener('change', toggleBanco);
        });

        toggleBanco();
    });
</script>
@endpush


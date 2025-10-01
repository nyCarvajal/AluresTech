@php
    $editando = $salida->exists;
    $usuarioAutenticado = auth()->user();
    $origenSeleccionado = old('origen');

 $valorAnterior = old('valor', $editando ? $salida->valor : null);
    if ($valorAnterior === null || $valorAnterior === '') {
        $valorEntero = '';
    } else {
        $valorEntero = (int) preg_replace('/[^\d]/', '', (string) $valorAnterior);
    }
    $valorFormateado = $valorEntero === '' ? '' : number_format($valorEntero, 0, ',', '.');

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
    >
        <option value="" {{ old('tercero_id', $editando ? $salida->tercero_id : '') === '' ? 'selected' : '' }}>
            — Sin proveedor —
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
    <label for="cuenta_bancaria" class="form-label">Cuenta bancaria</label>
    <select
        id="cuenta_bancaria"
        name="cuenta_bancaria"
        class="form-select"
        {{ $origenSeleccionado === 'caja' ? 'disabled' : '' }}
    >
        <option value="" disabled {{ old('cuenta_bancaria', $editando ? $salida->cuenta_bancaria : '') === '' ? 'selected' : '' }}>
            — Selecciona banco —
        </option>
        @foreach($bancos as $banco)
            <option
                value="{{ $banco->id }}"
                {{ (string) old('cuenta_bancaria', $editando ? $salida->cuenta_bancaria : '') === (string) $banco->id ? 'selected' : '' }}
            >
                {{ $banco->nombre }}
            </option>
        @endforeach
    </select>
</div>

{{-- Valor --}}
<div class="mb-3">
 <label for="valor_display" class="form-label">Valor</label>
    <input
        type="text"
        inputmode="numeric"
        id="valor_display"
        class="form-control"
        value="{{ $valorFormateado }}"
        autocomplete="off"
        placeholder="0"
    >
    <input
        type="hidden"
        id="valor"
        name="valor"
        value="{{ $valorEntero }}"

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
        <select name="responsable" class="form-select" required>
            <option value="" disabled {{ old('responsable', $salida->responsable) === null ? 'selected' : '' }}>
                — Selecciona usuario —
            </option>
            @foreach($usuarios as $usuario)
                <option
                    value="{{ $usuario->id }}"
                    {{ (string) old('responsable', $salida->responsable) === (string) $usuario->id ? 'selected' : '' }}
                >
      {{ $usuario->nombre }}

                </option>
            @endforeach
        </select>
    @else
        <input
            type="text"
            class="form-control"
 value="{{ optional($usuarioAutenticado)->nombre }}"

            disabled
        >
        <input
            type="hidden"
            name="responsable"
            value="{{ old('responsable', optional($usuarioAutenticado)->id) }}"
        >
    @endif
</div>

@push('scripts')
<script>
    document.addEventListener('DOMContentLoaded', function () {
        const radiosOrigen = document.querySelectorAll('input[name="origen"]');
        const selectBanco = document.getElementById('cuenta_bancaria');
        const contenedorBanco = document.getElementById('contenedor-cuenta-bancaria');
        const valorDisplay = document.getElementById('valor_display');
        const valorHidden = document.getElementById('valor');


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


        if (valorDisplay && valorHidden) {
            const formatter = new Intl.NumberFormat('es-CO');

            const aplicarFormato = () => {
                const soloDigitos = valorDisplay.value.replace(/[^0-9]/g, '');
                valorHidden.value = soloDigitos ? parseInt(soloDigitos, 10) : '';
                valorDisplay.value = soloDigitos ? formatter.format(parseInt(soloDigitos, 10)) : '';

                if (valorDisplay.value.length) {
                    const posicion = valorDisplay.value.length;
                    requestAnimationFrame(() => {
                        valorDisplay.setSelectionRange(posicion, posicion);
                    });
                }
            };

            if (valorHidden.value) {
                valorDisplay.value = formatter.format(parseInt(valorHidden.value, 10));
            }

            valorDisplay.addEventListener('input', aplicarFormato);
            valorDisplay.addEventListener('blur', aplicarFormato);
        }

    });
</script>
@endpush


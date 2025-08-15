{{-- resources/views/pagos/_form.blade.php --}}

@php
    // Si estamos en edición, $pago existe; en creación no.
    $isEdit = isset($pago);
@endphp

<input type="hidden" name="cuenta" value="{{ old('cuenta', $isEdit ? $pago->cuenta : request('cuenta')) }}">
<div class="card card-body">
<div class="row">
    {{-- FECHA Y HORA --}}
    <div class="col-md-6 mb-3">
        <label for="fecha_hora" class="form-label">
            <i class="fa fa-calendar-alt me-1"></i> Fecha y Hora
        </label>
        <input
            type="datetime-local"
            name="fecha_hora"
            id="fecha_hora"
            class="form-control"
            value="{{ old('fecha_hora', $isEdit ? $pago->fecha_hora->format('Y-m-d\TH:i') : '') }}"
            required
        >
    </div>

    {{-- VALOR --}}
    <div class="col-md-6 mb-3">
        <label for="valor" class="form-label">
            <i class="fa fa-dollar-sign me-1"></i> Valor
        </label>
        <input
            type="text"
            name="valor"
            id="valor"
            class="form-control"
            placeholder="0"
            value="{{ old('valor', $isEdit ? $pago->valor : '') }}"
            required
        >
    </div>
</div>

<div class="row">
    {{-- MEDIO DE PAGO --}}
    <div class="col-md-6 mb-3">
        <label for="medio_pago" class="form-label">
            <i class="fa fa-credit-card me-1"></i> Medio de Pago
        </label>
        <select name="medio_pago" id="medio_pago" class="form-select" required>
            @php
                $oldMedio = old('medio_pago', $isEdit ? $pago->medio_pago : '');
            @endphp
            <option value="" disabled {{ $oldMedio == '' ? 'selected' : '' }}>-- Selecciona --</option>
            <option value="efectivo" {{ $oldMedio == 'efectivo' ? 'selected' : '' }}>Efectivo</option>
            <option value="tarjeta"   {{ $oldMedio == 'tarjeta' ? 'selected' : '' }}>Tarjeta</option>
            <option value="transferencia" {{ $oldMedio == 'transferencia' ? 'selected' : '' }}>Transferencia</option>
            <option value="otro" {{ $oldMedio == 'otro' ? 'selected' : '' }}>Otro</option>
        </select>
    </div>

    {{-- BANCO --}}
    <div class="col-md-6 mb-3">
        <label for="banco" class="form-label">
            <i class="fa fa-building me-1"></i> Banco
        </label>

        <select name="banco" id="banco" class="form-select">
            @php
                $oldBanco = old('banco', $isEdit ? $pago->banco : '');
            @endphp
            <option value="" disabled {{ $oldBanco == '' ? 'selected' : '' }}>-- Selecciona --</option>
            @foreach($bancos as $b)
                <option value="{{ $b->id }}" {{ $oldBanco == $b->id ? 'selected' : '' }}>{{ $b->nombre }}</option>
            @endforeach
        </select>

        
    </div>
</div>





<div class="row">
    {{-- ESTADO --}}
    <div class="col-md-6 mb-3">
        <label for="estado" class="form-label">
            <i class="fa fa-info-circle me-1"></i> Estado
        </label>
        <select name="estado" id="estado" class="form-select" required>
            @php
                $oldEstado = old('estado', $isEdit ? $pago->estado : 1);
            @endphp
            <option value="1" {{ $oldEstado == 1 ? 'selected' : '' }}>Activo</option>
            <option value="0" {{ $oldEstado == 0 ? 'selected' : '' }}>Anulado</option>
        </select>
    </div>
</div>

{{-- BOTONES --}}
<div class="mt-4">
    <button type="submit" class="btn btn-success">
        <i class="fa fa-save me-1"></i>
        {{ $isEdit ? 'Actualizar' : 'Guardar' }}
    </button>
    <a href="{{ route('pagos.index') }}" class="btn btn-secondary">
        <i class="fa fa-arrow-left me-1"></i> Cancelar
    </a>
</div>

</div>

<script>
    document.addEventListener('DOMContentLoaded', function() {
        const form = document.getElementById('pago-form');
        const valorInput = document.getElementById('valor');
        form.addEventListener('submit', function() {
            valorInput.value = valorInput.value.replace(/[^0-9]/g, '');
        });
    });
</script>


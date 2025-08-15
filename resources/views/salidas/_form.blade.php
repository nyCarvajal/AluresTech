@php
    // $salida viene siempre; en create() es un modelo vacío
    $editando = $salida->exists;   // true = editar, false = crear
@endphp

<form action="{{ $editando
                ? route('salidas.update', $salida)   // necesita ID
                : route('salidas.store') }}"          // alta nueva
      method="POST">

  @csrf
  @if($editando)
      @method('PUT')  {{-- spoof HTTP PUT para la actualización --}}
  @endif

  {{-- CONCEPTO --}}
  <div class="mb-3">
    <label for="concepto">Concepto</label>
    <input type="text" name="concepto"
           value="{{ old('concepto', $editando ? $salida->concepto : '') }}"
           class="form-control" required>
  </div>

  {{-- FECHA --}}
  <div class="mb-3">
    <label>Fecha</label>
    <input type="date" name="fecha"
           value="{{ old('fecha',
                         ($editando && $salida->fecha)
                           ? $salida->fecha->format('Y-m-d')
                           : '') }}"
           class="form-control" required>
  </div>

  {{-- CUENTA BANCARIA --}}
  <div class="mb-3">
    <label>Cuenta Bancaria</label>
    <select name="cuenta_bancaria_id" class="form-select" required>
      <option value="" disabled {{ old('cuenta_bancaria_id',
             $editando ? $salida->cuenta_bancaria_id : '')=='' ? 'selected':'' }}>
        — Selecciona banco —
      </option>
      @foreach($bancos as $b)
        <option value="{{ $b->id }}"
          {{ old('cuenta_bancaria_id',
                 $editando ? $salida->cuenta_bancaria_id : '') == $b->id ? 'selected':'' }}>
          {{ $b->nombre }}
        </option>
      @endforeach
    </select>
  </div>

  {{-- VALOR --}}
  <div class="mb-3">
    <label>Valor</label>
    <input type="number" step="0.01" min="0" name="valor"
           value="{{ old('valor', $editando ? $salida->valor : '') }}"
           class="form-control" required>
  </div>

  {{-- CUENTA CONTABLE --}}
  <div class="mb-3">
    <label>Cuenta Contable</label>
    <input type="text" name="cuenta_contable"
           value="{{ old('cuenta_contable', $editando ? $salida->cuenta_contable : '') }}"
           class="form-control" required>
  </div>

  {{-- OBSERVACIONES --}}
  <div class="mb-3">
    <label>Observaciones</label>
    <textarea name="observaciones" class="form-control" rows="3">
      {{ old('observaciones', $editando ? $salida->observaciones : '') }}
    </textarea>
  </div>

  {{-- RESPONSABLE --}}
  <div class="mb-3">
    <label>Responsable</label>
    <select name="responsable_id" class="form-select" required>
      <option value="" disabled {{ old('responsable_id',
             $editando ? $salida->responsable_id : '')=='' ? 'selected':'' }}>
        — Selecciona usuario —
      </option>
      @foreach($usuarios as $u)
        <option value="{{ $u->id }}"
          {{ old('responsable_id',
                 $editando ? $salida->responsable_id : '') == $u->id ? 'selected':'' }}>
          {{ $u->name }}
        </option>
      @endforeach
    </select>
  </div>

  {{-- TERCERO --}}
  <div class="mb-3">
    <label>Tercero (Proveedor)</label>
    <select name="tercero_id" class="form-select" required>
      <option value="" disabled {{ old('tercero_id',
             $editando ? $salida->tercero_id : '')=='' ? 'selected':'' }}>
        — Selecciona proveedor —
      </option>
      @foreach($proveedores as $proveedor)
        <option value="{{ $proveedor->id }}"
          {{ old('tercero_id',
                 $editando ? $salida->tercero_id : '') == $proveedor->id ? 'selected':'' }}>
          {{ $proveedor->nombre }}
        </option>
      @endforeach
    </select>
  </div>

 
</form>

{{-- Sólo los campos, sin <form> ni botones --}}
@csrf

{{-- Nombre --}}
<div class="mb-3">
  <label for="nombre" class="form-label">Nombre</label>
  <input type="text" id="nombre" name="nombre"
         class="form-control @error('nombre') is-invalid @enderror"
         value="{{ old('nombre', $cancha->nombre ?? '') }}"
         placeholder="Nombre de la cancha" required>
  @error('nombre')<div class="invalid-feedback">{{ $message }}</div>@enderror
</div>

{{-- Deporte --}}
<div class="mb-3">
  <label for="deporte_id" class="form-label">Deporte</label>
  <select id="deporte_id" name="deporte_id"
          class="form-select @error('deporte_id') is-invalid @enderror" required>
    <option value="">Selecciona deporte</option>
    @foreach($deportes as $deporte)
      <option value="{{ $deporte->id }}"
        {{ old('deporte_id', $cancha->deporte_id ?? '') == $deporte->id ? 'selected' : '' }}>
        {{ $deporte->deporte }}
      </option>
    @endforeach
  </select>
  @error('deporte_id')<div class="invalid-feedback">{{ $message }}</div>@enderror
</div>

<div class="row">
  {{-- Capacidad --}}
  <div class="col-md-6 mb-3">
    <label for="capacidad" class="form-label">Capacidad</label>
    <input type="number" id="capacidad" name="capacidad"
           class="form-control @error('capacidad') is-invalid @enderror"
           value="{{ old('capacidad', $cancha->capacidad ?? 0) }}"
           min="0" step="1" required>
    @error('capacidad')<div class="invalid-feedback">{{ $message }}</div>@enderror
  </div>

  {{-- Valor Hora --}}
  <div class="col-md-6 mb-3">
    <label for="valor" class="form-label">Valor Hora</label>
    <input type="number" id="valor" name="valor"
           class="form-control @error('valor') is-invalid @enderror"
           value="{{ old('valor', $cancha->valor ?? '0.00') }}"
           min="0" step="0.01" required>
    @error('valor')<div class="invalid-feedback">{{ $message }}</div>@enderror
  </div>
</div>

{{-- Este partial incluye únicamente los inputs para crear/editar Membresía --}}
{{-- Recibe: $membresia (instancia o null) --}}

@php
    $m = $membresia ?? null;
@endphp

<div class="mb-3">
    <label for="descripcion" class="form-label">Descripción</label>
    <input
        type="text"
        class="form-control @error('descripcion') is-invalid @enderror"
        id="descripcion"
        name="descripcion"
        value="{{ old('descripcion', $m->descripcion ?? '') }}"
        placeholder="Ingrese descripción"
        required
    >
    @error('descripcion')
        <div class="invalid-feedback">{{ $message }}</div>
    @enderror
</div>

<div class="mb-3">
    <label for="clases" class="form-label">Clases</label>
    <input
        type="number"
        class="form-control @error('clases') is-invalid @enderror"
        id="clases"
        name="clases"
        value="{{ old('clases', $m->clases ?? '') }}"
        min="0"
        required
    >
    @error('clases')
        <div class="invalid-feedback">{{ $message }}</div>
    @enderror
</div>

<div class="mb-3">
    <label for="reservas" class="form-label">Reservas</label>
    <input
        type="number"
        class="form-control @error('reservas') is-invalid @enderror"
        id="reservas"
        name="reservas"
        value="{{ old('reservas', $m->reservas ?? '') }}"
        min="0"
        required
    >
    @error('reservas')
        <div class="invalid-feedback">{{ $message }}</div>
    @enderror
</div>

<div class="mb-3">
    <label for="valor" class="form-label">Valor</label>
    <input
        type="number"
        step="0.01"
        class="form-control @error('valor') is-invalid @enderror"
        id="valor"
        name="valor"
        value="{{ old('valor', $m->valor ?? '') }}"
        min="0"
        required
    >
    @error('valor')
        <div class="invalid-feedback">{{ $message }}</div>
    @enderror
</div>

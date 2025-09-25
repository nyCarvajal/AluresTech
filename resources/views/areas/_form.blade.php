@csrf
<div class="mb-3">
    <label for="descripcion" class="form-label">Descripción</label>
    <input type="text" name="descripcion" id="descripcion" class="form-control @error('descripcion') is-invalid @enderror" value="{{ old('descripcion', $area->descripcion ?? '') }}" required maxlength="300">
    @error('descripcion')
        <div class="invalid-feedback">{{ $message }}</div>
    @enderror
</div>

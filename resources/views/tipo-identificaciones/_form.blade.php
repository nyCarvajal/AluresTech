@csrf
<div class="mb-3">
    <label for="tipo" class="form-label">Tipo</label>
    <input type="text" name="tipo" id="tipo" class="form-control @error('tipo') is-invalid @enderror" value="{{ old('tipo', $tipoIdentificacion->tipo ?? '') }}" required maxlength="100">
    @error('tipo')
        <div class="invalid-feedback">{{ $message }}</div>
    @enderror
</div>

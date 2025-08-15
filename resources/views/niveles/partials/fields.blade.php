{{-- Este partial incluye únicamente los inputs que se usan en create y en edit --}}
{{-- Recibe: $nivel (instancia o null) --}}

@php
    // Si $nivel no está definido, lo dejamos como null
    $nivelObj = $nivel ?? null;
@endphp

<div class="mb-3">
    <label for="nivel" class="form-label">Nivel</label>
    <input 
        type="text" 
        class="form-control @error('nivel') is-invalid @enderror" 
        id="nivel" 
        name="nivel" 
        value="{{ old('nivel', $nivelObj->nivel ?? '') }}" 
        placeholder="Ingrese el nivel" 
        required
    >
    @error('nivel')
        <div class="invalid-feedback">{{ $message }}</div>
    @enderror
</div>

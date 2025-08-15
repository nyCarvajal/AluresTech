
<div class="mb-3">
  <label for="tipo_documento_id">Tipo Documento</label>
  <select id="tipo_documento_id"
          name="tipo_documento_id"
          class="form-select @error('tipo_documento_id') is-invalid @enderror"
          required>
    <option value="" disabled>-- Selecciona tipo --</option>
    @foreach($tiposDoc as $td)
      <option value="{{ $td->id }}"
        {{ old('tipo_documento_id', $proveedor->tipo_documento_id ?? '') == $td->id ? 'selected':'' }}>
        {{ $td->tipo }}
      </option>
    @endforeach
  </select>
  @error('tipo_documento_id')
    <div class="invalid-feedback">{{ $message }}</div>
  @enderror
</div>

<div class="mb-3">
  <label for="numero_documento">Número Documento</label>
  <input type="text"
         id="numero_documento"
         name="numero_documento"
         value="{{ old('numero_documento', $proveedor->numero_documento ?? '') }}"
         class="form-control @error('numero_documento') is-invalid @enderror"
         required>
  @error('numero_documento')
    <div class="invalid-feedback">{{ $message }}</div>
  @enderror
</div>

<div class="mb-3">
  <label for="nombre">Nombre</label>
  <input type="text"
         id="nombre"
         name="nombre"
         value="{{ old('nombre', $proveedor->nombre ?? '') }}"
         class="form-control @error('nombre') is-invalid @enderror"
         required>
  @error('nombre')
    <div class="invalid-feedback">{{ $message }}</div>
  @enderror
</div>

<div class="mb-3">
  <label for="regimen">Régimen</label>
  <select id="regimen"
          name="regimen"
          class="form-select @error('regimen') is-invalid @enderror"
          required>
    @foreach($regimenOpciones as $key => $label)
      <option value="{{ $key }}"
        {{ old('regimen', $proveedor->regimen ?? '') == $key ? 'selected':'' }}>
        {{ $label }}
      </option>
    @endforeach
  </select>
  @error('regimen')
    <div class="invalid-feedback">{{ $message }}</div>
  @enderror
</div>

<div class="form-check mb-3">
  <input type="checkbox"
         id="responsable_iva"
         name="responsable_iva"
         value="1"
         class="form-check-input @error('responsable_iva') is-invalid @enderror"
         {{ old('responsable_iva', $proveedor->responsable_iva ?? false) ? 'checked' : '' }}>
  <label for="responsable_iva" class="form-check-label">Responsable de IVA</label>
  @error('responsable_iva')
    <div class="invalid-feedback d-block">{{ $message }}</div>
  @enderror
</div>

<div class="mb-3">
  <label for="direccion">Dirección</label>
  <input type="text"
         id="direccion"
         name="direccion"
         value="{{ old('direccion', $proveedor->direccion ?? '') }}"
         class="form-control @error('direccion') is-invalid @enderror"
         required>
  @error('direccion')
    <div class="invalid-feedback">{{ $message }}</div>
  @enderror
</div>

<div class="mb-3">
  <label for="departamento_id">Departamento</label>
  <select id="departamento_id"
          name="departamento_id"
          class="form-select @error('departamento_id') is-invalid @enderror">
    <option value="" disabled>-- Departamento --</option>
    @foreach($departamentos as $dep)
      <option value="{{ $dep->id }}"
        {{ old('departamento_id') == $dep->id ? 'selected':'' }}>
        {{ $dep->nombre }}
      </option>
    @endforeach
  </select>
  @error('departamento_id')
    <div class="invalid-feedback">{{ $message }}</div>
  @enderror
</div>

<div class="mb-3">
  <label for="municipio_id">Ciudad</label>
  <select id="municipio_id"
          name="municipio_id"
          class="form-select @error('municipio_id') is-invalid @enderror"
          required>
    <option value="" disabled>-- Selecciona ciudad --</option>
  </select>
  @error('municipio_id')
    <div class="invalid-feedback">{{ $message }}</div>
  @enderror
</div>

<script>
  document.addEventListener('DOMContentLoaded', function(){
    const deps = @json($departamentos);
    const munis = @json($municipios);
    const depSelect = document.getElementById('departamento_id');
    const muniSelect = document.getElementById('municipio_id');

    function loadCities() {
      const depId = depSelect.value;
      muniSelect.innerHTML = '<option value="" disabled>-- Selecciona ciudad --</option>';
      munis.filter(m => m.departamento_id == depId)
        .forEach(m => {
          const opt = document.createElement('option');
          opt.value = m.id;
          opt.text = m.nombre;
          if(m.id == {{ old('municipio_id', $proveedor->municipio_id ?? 'null') }}) opt.selected = true;
          muniSelect.appendChild(opt);
        });
    }

    depSelect.addEventListener('change', loadCities);
    if(depSelect.value) loadCities();
  });
</script>

{{-- resources/views/alumnos/edit.blade.php --}}
@extends('layouts.vertical', ['subtitle' => 'Editar Cliente'])

@section('css')
  <!-- Flatpickr CSS -->
  <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/flatpickr/dist/flatpickr.min.css" />
  <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/flatpickr/dist/themes/dark.css" />
  
@endsection

@section('content')
<div class="row">
  <div class="col-12">
    <div class="card">
      <div class="card-header">
        <h4 class="card-title">Editar Cliente</h4>
      </div>
      <div class="card-body">
        <form method="POST" action="{{ route('alumnos.update', $alumno) }}"  enctype="multipart/form-data">
          @csrf
          @method('PUT')
		  
		   {{-- tu input de foto --}}
  <div class="mb-3">
    <label for="foto" class="form-label">Foto del jugador</label>
    <input type="file"
           name="foto"
           id="foto"
           accept="image/*"
           class="form-control @error('foto') is-invalid @enderror">
    @error('foto')
      <div class="invalid-feedback">{{ $message }}</div>
    @enderror
  </div>

          {{-- Tipo y Número Identificación --}}
          <div class="row">
            <div class="col-md-6 mb-3">
              <label for="tipo_identificacion" class="form-label">Tipo de Identificación</label>
              <select id="tipo_identificacion" name="tipo_identificacion" class="form-select @error('tipo_identificacion') is-invalid @enderror">
                <option value="">Selecciona tipo</option>
                @foreach($tipoIdentificaciones as $tipo)
                  <option value="{{ $tipo->id }}" {{ old('tipo_identificacion', $alumno->tipo_identificacion)==$tipo->id?'selected':'' }}>{{ $tipo->tipo }}</option>
                @endforeach
              </select>
              @error('tipo_identificacion')<div class="invalid-feedback">{{ $message }}</div>@enderror
            </div>
            <div class="col-md-6 mb-3">
              <label for="numero_identificacion" class="form-label">Número de Identificación</label>
              <input type="text" id="numero_identificacion" name="numero_identificacion" class="form-control @error('numero_identificacion') is-invalid @enderror" value="{{ old('numero_identificacion', $alumno->numero_identificacion) }}">
              @error('numero_identificacion')<div class="invalid-feedback">{{ $message }}</div>@enderror
            </div>
          </div>

          {{-- Nivel Académico --}}
          <div class="mb-3">
            <label for="nivel" class="form-label">Nivel</label>
            <select id="nivel" name="nivel_id" class="form-select @error('nivel') is-invalid @enderror">
              <option value="">Selecciona nivel</option>
              @foreach($niveles as $nivel)
                <option value="{{ $nivel->id }}" {{ old('nivel', $alumno->nivel_id)==$nivel->id?'selected':'' }}>{{ $nivel->nivel }}</option>
              @endforeach
            </select>
            @error('nivel')<div class="invalid-feedback">{{ $message }}</div>@enderror
          </div>
		 


          {{-- Nombres y Apellidos --}}
          <div class="row">
            <div class="col-md-6 mb-3">
              <label for="nombres" class="form-label">Nombres</label>
              <input type="text" id="nombres" name="nombres" class="form-control @error('nombres') is-invalid @enderror" value="{{ old('nombres', $alumno->nombres) }}">
              @error('nombres')<div class="invalid-feedback">{{ $message }}</div>@enderror
            </div>
            <div class="col-md-6 mb-3">
              <label for="apellidos" class="form-label">Apellidos</label>
              <input type="text" id="apellidos" name="apellidos" class="form-control @error('apellidos') is-invalid @enderror" value="{{ old('apellidos', $alumno->apellidos) }}">
              @error('apellidos')<div class="invalid-feedback">{{ $message }}</div>@enderror
            </div>
          </div>

          {{-- Correo y WhatsApp --}}
          <div class="row">
            <div class="col-md-6 mb-3">
              <label for="correo" class="form-label">Correo</label>
              <input type="email" id="correo" name="correo" class="form-control @error('correo') is-invalid @enderror" value="{{ old('correo', $alumno->correo) }}" placeholder="nombre@dominio.com" required>
              @error('correo')<div class="invalid-feedback">{{ $message }}</div>@enderror
            </div>
            <div class="col-md-6 mb-3">
              <label for="whatsapp" class="form-label">WhatsApp</label>
              <input type="tel" id="whatsapp" name="whatsapp" class="form-control phone-input @error('whatsapp') is-invalid @enderror" value="{{ old('whatsapp', $alumno->whatsapp) }}" data-country="co" placeholder="(Código) Número">
              @error('whatsapp')<div class="invalid-feedback">{{ $message }}</div>@enderror
            </div>
          </div>

          {{-- Fecha de Nacimiento --}}
          <div class="mb-3">
            <label for="fecha_nacimiento" class="form-label">Fecha de Nacimiento</label>
            <input type="text" id="fecha_nacimiento" name="fecha_nacimiento" class="form-control @error('fecha_nacimiento') is-invalid @enderror" value="{{ old('fecha_nacimiento', $alumno->fecha_nacimiento) }}" placeholder="Selecciona fecha">
            @error('fecha_nacimiento')<div class="invalid-feedback">{{ $message }}</div>@enderror
          </div>

          {{-- Dirección --}}
          <div class="mb-3">
            <label for="direccion" class="form-label">Dirección</label>
            <input type="text" id="direccion" name="direccion" class="form-control @error('direccion') is-invalid @enderror" value="{{ old('direccion', $alumno->direccion) }}">
            @error('direccion')<div class="invalid-feedback">{{ $message }}</div>@enderror
          </div>

          {{-- Ubicación: País, Departamento, Municipio --}}
          <div class="row">
            <div class="col-md-4 mb-3">
              <label for="pais" class="form-label">País</label>
              <select id="pais" name="pais" class="form-select @error('pais') is-invalid @enderror">
                <option value="">Selecciona país</option>
                @foreach($paises as $pais)
                  <option value="{{ $pais->id }}" {{ old('pais', $alumno->pais)==$pais->id?'selected':'' }}>{{ $pais->nombre }}</option>
                @endforeach
              </select>
              @error('pais')<div class="invalid-feedback">{{ $message }}</div>@enderror
            </div>
            <div class="col-md-4 mb-3">
              <label for="departamento" class="form-label">Departamento</label>
              <select id="departamento" name="departamento" class="form-select @error('departamento') is-invalid @enderror">
                <option value="">Selecciona departamento</option>
              </select>
              @error('departamento')<div class="invalid-feedback">{{ $message }}</div>@enderror
            </div>
            <div class="col-md-4 mb-3">
              <label for="municipio" class="form-label">Municipio</label>
              <select id="municipio" name="municipio" class="form-select @error('municipio') is-invalid @enderror">
                <option value="">Selecciona municipio</option>
              </select>
              @error('municipio')<div class="invalid-feedback">{{ $message }}</div>@enderror
            </div>
          </div>

          {{-- Sexo --}}
          <div class="mb-3">
            <label for="sexo" class="form-label">Sexo</label>
            <select id="sexo" name="sexo" class="form-select @error('sexo') is-invalid @enderror">
              <option value="">Selecciona sexo</option>
              <option value="M" {{ old('sexo', $alumno->sexo)=='M'?'selected':'' }}>Masculino</option>
              <option value="F" {{ old('sexo', $alumno->sexo)=='F'?'selected':'' }}>Femenino</option>
            </select>
            @error('sexo')<div class="invalid-feedback">{{ $message }}</div>@enderror
          </div>

          <button type="submit" class="btn btn-primary">Actualizar Cliente</button>
          <a href="{{ route('alumnos.index') }}" class="btn btn-secondary ms-2">Cancelar</a>
        </form>
      </div>
    </div>
  </div>
</div>
@endsection

@section('scripts')
  <!-- Flatpickr JS -->
  <script src="https://cdn.jsdelivr.net/npm/flatpickr"></script>
  <!-- intl-tel-input JS -->
  <script src="https://cdnjs.cloudflare.com/ajax/libs/intl-tel-input/17.0.8/js/intlTelInput.min.js"></script>
  <script>
    document.addEventListener('DOMContentLoaded', function() {
      // Datepicker
      flatpickr("#fecha_nacimiento", { altInput: true, altFormat: "F j, Y", dateFormat: "Y-m-d", maxDate: "today" });

      // WhatsApp
      const phoneInput = document.querySelector('#whatsapp');
      if (phoneInput) window.intlTelInput(phoneInput, { initialCountry: phoneInput.dataset.country, separateDialCode: true, utilsScript: 'https://cdnjs.cloudflare.com/ajax/libs/intl-tel-input/17.0.8/js/utils.js' });

      // Dependent selects
      const paisSelect = document.querySelector('#pais');
      const deptoSelect = document.querySelector('#departamento');
      const muniSelect = document.querySelector('#municipio');
      const defaultDepto = "{{ old('departamento', $alumno->departamento) }}";
      const defaultMuni = "{{ old('municipio', $alumno->municipio) }}";

      async function cargarDepartamentos() {
        const paisId = paisSelect.value;
        deptoSelect.innerHTML = '<option value="">Cargando...</option>';
        muniSelect.innerHTML = '<option value="">Selecciona municipio</option>';
        if (!paisId) { deptoSelect.innerHTML = '<option value="">Selecciona departamento</option>'; return; }
        const res = await fetch(`/departamentos?pais_id=${paisId}`);
        const data = await res.json();
        deptoSelect.innerHTML = '<option value="">Selecciona departamento</option>';
        data.forEach(d => {
          const sel = defaultDepto == d.id.toString() ? 'selected' : '';
          deptoSelect.insertAdjacentHTML('beforeend', `<option value="${d.id}" ${sel}>${d.nombre}</option>`);
        });
        cargarMunicipios();
      }

      async function cargarMunicipios() {
        const deptoId = deptoSelect.value;
        muniSelect.innerHTML = '<option value="">Cargando...</option>';
        if (!deptoId) { muniSelect.innerHTML = '<option value="">Selecciona municipio</option>'; return; }
        const res = await fetch(`/municipios?departamento_id=${deptoId}`);
        const data = await res.json();
        muniSelect.innerHTML = '<option value="">Selecciona municipio</option>';
        data.forEach(m => {
          const sel = defaultMuni == m.id.toString() ? 'selected' : '';
          muniSelect.insertAdjacentHTML('beforeend', `<option value="${m.id}" ${sel}>${m.nombre}</option>`);
        });
      }

      paisSelect.addEventListener('change', cargarDepartamentos);
      deptoSelect.addEventListener('change', cargarMunicipios);
      if (paisSelect.value) cargarDepartamentos();
    });
  </script>
@endsection

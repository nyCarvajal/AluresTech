@extends('layouts.vertical', ['subtitle' => 'Editar Suscripción'])

@section('content')
<div class="row">
  <div class="col-lg-6 mx-auto">
    <div class="card">
      <div class="card-header"><h4>Editar suscripción</h4></div>

      <div class="card-body">
        <form method="POST"
              action="{{ route('membresia-cliente.update', $membresia_cliente) }}">
          @csrf
          @method('PUT')

          {{-- Plan / Membresía --}}
          <div class="mb-3">
            <b>Plan: 
          
                  {{ $membresia_cliente->paquete->descripcion  }} 
            
</b>
          {{-- Clases y Reservas --}}
          <div class="row">
            <div class="col-md-6 mb-3">
              <label class="form-label">Clases </label>
              <input type="number" name="clases"
                     value="{{ old('clases', $membresia_cliente->clases) }}"
                     class="form-control @error('clases') is-invalid @enderror">
              @error('clases')<div class="invalid-feedback">{{ $message }}</div>@enderror
            </div>

            <div class="col-md-6 mb-3">
              <label class="form-label">Reservas </label>
              <input type="number" name="reservas"
                     value="{{ old('reservas', $membresia_cliente->reservas) }}"
                     class="form-control @error('reservas') is-invalid @enderror">
              @error('reservas')<div class="invalid-feedback">{{ $message }}</div>@enderror
            </div>
          </div>
		  
          {{-- Clases y Reservas --}}
          <div class="row">
            <div class="col-md-6 mb-3">
              <label class="form-label">Clases Vistas</label>
              <input type="number" name="clasesVistas"
                     value="{{ old('clases', $membresia_cliente->clasesVistas) }}"
                     class="form-control @error('clases') is-invalid @enderror">
              @error('clases')<div class="invalid-feedback">{{ $message }}</div>@enderror
            </div>

            <div class="col-md-6 mb-3">
              <label class="form-label">Reservas Usadas</label>
              <input type="number" name="numReservas"
                     value="{{ old('reservas', $membresia_cliente->numReservas) }}"
                     class="form-control @error('reservas') is-invalid @enderror">
              @error('reservas')<div class="invalid-feedback">{{ $message }}</div>@enderror
            </div>
          </div>
		  
		  

          {{-- Estado --}}
         <div class="mb-3">
  <label class="form-label">Estado</label>
  <select name="estado"
          class="form-select @error('estado') is-invalid @enderror">
    @php
      // valor => etiqueta
      $estados = ['1' => 'Activa', '0' => 'Inactiva'];
    @endphp

    @foreach ($estados as $valor => $etiqueta)
      <option value="{{ $valor }}"
              {{ old('estado', $membresia_cliente->estado) == $valor ? 'selected' : '' }}>
        {{ $etiqueta }}
      </option>
    @endforeach
  </select>
  @error('estado')<div class="invalid-feedback">{{ $message }}</div>@enderror
</div>


          <button type="submit" class="btn btn-primary">Guardar cambios</button>
          <a href="{{ url()->previous() }}" class="btn btn-secondary ms-2">Cancelar</a>
        </form>
      </div>
    </div>
  </div>
</div>
@endsection

<div class="modal fade" id="reservationModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog">
      <div class="modal-content">
        <form id="reservationForm" 
		 action="{{ route('reservas.store') }}"
		method="POST">
          @csrf
          <input type="hidden" name="_method" id="reservationMethod" value="POST">
          <div class="modal-header">
           <h5 class="modal-title" id="reservationModalLabel">Nueva Cita</h5>
            <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
          </div>
          <div class="modal-body">
		  
 <label for="eventType" class="form-label">Tipo de Cita</label>
            
            <select id="eventType" name="type" class="form-select mb-3">
@foreach($tipocitas as $tc)
  <option value="{{ $tc->nombre }}">{{ $tc->nombre }}</option>
 @endforeach
</select>


{{-- Fecha y hora de inicio --}}
{{-- Fecha --}}
 <label>Fecha y hora</label>
<input type="date" id="reservaFecha" name="fecha" class="form-control" />

{{-- Hora --}}
<select id="reservaHora" name="hora" class="form-select" required>
  <option value="">-- Elige hora --</option>
</select>
{{-- Campo oculto que realmente irá al controlador --}}
<input type="hidden" name="start" id="start">


{{-- Duración --}}
<div class="mb-3">
  <label for="reservaDuracion" class="form-label">Duración</label>
  <select id="reservaDuracion" name="duration" class="form-select" required>
    <option value="60">60 minutos</option>
    <option value="90">90 minutos</option>
    <option value="120">120 minutos</option>
    <option value="180">180 minutos</option>
    <!-- añade más si quieres -->
  </select>
</div>

	
			
            <div class="mb-3">
              <label for="reservaEstado" class="form-label">Estado</label>
              <select id="reservaEstado" name="estado" class="form-select" required>
                <option value="Pendiente">Pendiente</option>
                <option value="Confirmada">Confirmada</option>
                <option value="No Asistida">No Asistida</option>
                                 <option value="Cancelada">Cancelada</option>
              </select>
            </div>
			
			



<!-- Cita & Clase: clientes -->

 <!-- === CAMPOS PARA “Clase” === -->
@php
    $stylistLabelSingular = $stylistLabelSingular ?? \App\Models\Peluqueria::defaultRoleLabel(\App\Models\Peluqueria::ROLE_STYLIST);
    $stylistLabelPlural = $stylistLabelPlural ?? \App\Models\Peluqueria::defaultRoleLabel(\App\Models\Peluqueria::ROLE_STYLIST, true);
@endphp

         <!-- {{ $stylistLabelSingular }} -->
        <div id="fieldEntrenador" class="mb-3">
           <label for="entrenador" class="form-label">{{ $stylistLabelSingular }}</label>
            <select id="entrenador"
                    name="entrenador_id"
                    class="form-select">
                <option value="">Selecciona a tu {{ \Illuminate\Support\Str::lower($stylistLabelSingular) }}</option>
                @foreach($entrenadores as $u)
                  <option value="{{ $u->id }}">{{ $u->nombre }}</option>
                @endforeach
              </select>
            </div>

          <!-- Servicio -->
          <div id="fieldServicio" class="mb-3">
            <label for="servicio" class="form-label">Servicio</label>
            <select id="servicio"
                    name="servicio_id"
                    class="form-select">
              <option value="">Selecciona un servicio</option>
              @foreach($servicios as $servicio)
                <option value="{{ $servicio->id }}">{{ $servicio->nombre }}</option>
              @endforeach
            </select>
          </div>



          <div id="fieldCuenta" class="alert alert-info d-none" role="status">
            <div class="d-flex flex-wrap align-items-center justify-content-between gap-2">
              <span id="reservationCuentaLabel" class="fw-semibold"></span>
              <a id="reservationCuentaLink"
                 href="#"
                 target="_blank"
                 rel="noopener"
                 class="btn btn-sm btn-primary">
                Ver cuenta
              </a>
            </div>
          </div>

          </div>
          <div class="modal-footer d-flex justify-content-end">
            <div class="d-flex gap-2">
              <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">
                Cerrar
              </button>
              <button type="submit" class="btn btn-primary" id="reservationSubmit">
                Guardar
              </button>
            </div>
          </div>
        </form>
      </div>
    </div>
  </div>
 

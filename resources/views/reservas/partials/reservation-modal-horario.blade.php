{{-- resources/views/reservas/partials/reservation-modal-horario.blade.php --}}
<!-- en <head> de layouts/vertical.blade.php -->





<div class="modal fade" id="horarioReservationModal" tabindex="-1" aria-hidden="true">
  <div class="modal-dialog">
    <div class="modal-content">
      <form id="horarioReservationForm" action="{{ route('reservas.store') }}" method="POST">
        @csrf
        <input type="hidden" name="_method" id="horarioReservationMethod" value="POST">

        <div class="modal-header">
         <h5 class="modal-title">Nueva Cita (Horario)</h5>
          <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
        </div>
		
		 


        <div class="modal-body">
              <label for="reservaDuracion" class="form-label">Tipo de Cita</label>
            <input type="hidden" id="eventId" name="id">
			<select id="eventType" name="type" class="form-select mb-3">
  <option value="Reserva">Cita</option>
  <option value="Clase">Clase</option>
  <option value="Torneo">Torneo</option>
</select>

 {{-- Cancha --}}
          <div class="mb-3">
            <label for="horarioCancha" class="form-label">Cancha</label>
            <select name="cancha_id" id="horarioCancha" class="form-select" required>
              @foreach($canchas as $c)
                <option value="{{ $c->id }}">{{ $c->nombre }}</option>
              @endforeach
            </select>
          </div>


          {{-- Fecha --}}
          <div class="mb-3">
            <label for="horarioFecha" class="form-label">Fecha</label>
            <input type="date" name="fecha" id="horarioFecha" class="form-control" required>
          </div>

          {{-- Hora --}}
          <div class="mb-3">
            <label for="horarioHora" class="form-label">Hora</label>
            <select name="hora" id="horarioHora" class="form-select" required>
              <option value="">-- Elige hora --</option>
              {{-- Opciones cargadas vía AJAX o en blade --}}
            </select>
          </div>
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

<div class="form-check mb-3">
  <input type="checkbox"
         id="repeatReservation"
         name="repeat_enabled"
         value="1"
         class="form-check-input">
  <label for="repeatReservation" class="form-check-label">
    Cita periódica
  </label>
</div>
@php
  // Obtiene el 31 de diciembre del año actual en formato YYYY-MM-DD
  $defaultUntil = \Carbon\Carbon::now()->endOfYear()->toDateString();
@endphp
<div id="repeatOptions" style="display:none;">
  <div class="mb-3">
    <label for="repeatUntil" class="form-label">Fecha final</label>
    <input type="date"
           id="repeatUntil"
           name="repeat_until"
           class="form-control"
		   value="{{ $defaultUntil }}">
		   
  </div>
</div>



            <div class="mb-3">
              <label for="reservaEstado" class="form-label">Estado</label>
              <select id="reservaEstado" name="estado" class="form-select" required>
                <option value="Pendiente">Pendiente</option>
                <option value="Confirmada">Confirmada</option>
				 <option value="Cancelada">Cancelada</option>
              </select>
            </div>

  <!-- Estilista -->
          <div id="fieldEntrenador" class="mb-3 d-none">
            <label for="entrenador" class="form-label">Estilista</label>
            <select id="entrenador"
                    name="entrenador_id"
                    class="form-select">
              <option value="">Selecciona entrenador</option>
              @foreach($entrenadores as $u)
                <option value="{{ $u->id }}">{{ $u->nombre }}</option>
              @endforeach
            </select>
          </div>
         

         <div class="mb-3" id="fieldClientes">
  <label for="clientes" class="form-label">Clientes</label>
  <select
    id="clientes"
    name="clientes[]"
    class="form-select"
    multiple="multiple"
    style="width: 100%;"
    data-placeholder="Selecciona clientes..."
  >
    @foreach(\App\Models\Cliente::orderBy('nombres')->get() as $al)
      <option value="{{ $al->id }}">
        {{ $al->nombres }} {{ $al->apellidos }}
      </option>
    @endforeach
  </select>
</div>

        <div class="modal-footer">
          <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
          <button type="submit" class="btn btn-primary">Guardar</button>
        </div>
      </form>
    </div>
  </div>
</div>

@push('scripts')


  {{-- Cargar Axios desde CDN --}}
  <script src="https://cdn.jsdelivr.net/npm/axios/dist/axios.min.js"></script>
<script>  
  document.addEventListener('DOMContentLoaded', () => {
  const repeatCheckbox = document.getElementById('repeatReservation');
  const repeatOpts     = document.getElementById('repeatOptions');

  // Función para togglear la visibilidad
  function toggleRepeatOptions() {
    repeatOpts.style.display = repeatCheckbox.checked ? 'block' : 'none';
  }

  // Escucha el cambio del checkbox
  repeatCheckbox.addEventListener('change', toggleRepeatOptions);

  // Estado inicial al abrir la modal
  toggleRepeatOptions();
});


</script>


<script>
(() => {
  const fecha  = document.getElementById('horarioFecha');
  const hora   = document.getElementById('horarioHora');
  const start  = document.getElementById('start');
  const form   = document.getElementById('horarioReservationForm');

  function fusionar() {
    if (!fecha.value || !hora.value) {
      start.value = '';
      return;
    }
    // Formato "YYYY-MM-DDTHH:MM:00"
    start.value = `${fecha.value}T${hora.value}:00`;
  }

  fecha.addEventListener('change', fusionar);
  hora.addEventListener('change', fusionar);

  form.addEventListener('submit', e => {
    fusionar();
    if (!start.value) {
      e.preventDefault();
      alert('Selecciona fecha y hora.');
    }
  });
})();
</script>

<script>
document.addEventListener('DOMContentLoaded', () => {
  const modal            = document.getElementById('horarioReservationModal');
  const eventType        = document.getElementById('eventType');
  const fieldEntrenador  = document.getElementById('fieldEntrenador');
  const selEntrenador    = document.getElementById('entrenador');

  function toggleByType() {
    const isClase = eventType.value === 'Clase';

    // Mostrar/ocultar bloque
    fieldEntrenador.classList.toggle('d-none', !isClase);

    // Reglas de formulario
    if (isClase) {
      selEntrenador.removeAttribute('disabled');
      selEntrenador.setAttribute('required', 'required');
    } else {
      selEntrenador.value = '';
      selEntrenador.removeAttribute('required');
      selEntrenador.setAttribute('disabled', 'disabled');
    }
  }

  // Cambios en el select
  eventType.addEventListener('change', toggleByType);

  // Asegura estado correcto cada vez que se abre la modal
  modal.addEventListener('show.bs.modal', toggleByType);

  // Estado inicial por si la modal ya está en DOM
  toggleByType();
});
</script>



 

@endpush

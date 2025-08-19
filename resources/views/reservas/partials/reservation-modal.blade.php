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
		  
 <label for="reservaDuracion" class="form-label">Tipo de Cita</label>
            <input type="hidden" id="eventId" name="id">
			<select id="eventType" name="type" class="form-select mb-3">
 <option value="Reserva">Cita</option>
  <option value="Clase">Clase</option>
  <option value="Torneo">Torneo</option>
</select>

<!-- Cita & Clase: cancha -->
<div id="fieldCancha" class="mb-3">
  <label>Cancha</label>
  <select id="cancha" name="cancha_id" class="form-select">
    @foreach(\App\Models\Cancha::all() as $c)
      <option value="{{ $c->id }}">{{ $c->nombre }}</option>
    @endforeach
  </select>
</div>

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
				 <option value="Cancelada">Cancelada</option>
              </select>
            </div>
			
			



<!-- Cita & Clase: clientes -->

 <!-- === CAMPOS PARA “Clase” === -->
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

          <!-- Clientes (multi-select) -->
       <div id="fieldClientes" class="mb-3 d-none">
  <label for="clientes" class="form-label">Clientes</label>
  <select id="clientes"
          name="clientes[]"
          class="form-select"
          multiple
		  required>
    @foreach(\App\Models\Cliente::orderBy('nombres')->get() as $al)
      <option value="{{ $al->id }}">
        {{ $al->nombres }} {{ $al->apellidos }}
      </option>
    @endforeach
  </select>
</div>

          <!-- Componente para mostrar clientes seleccionados -->
<div id="selectedClientes" class="mb-2"></div>



<!-- Torneo : responsable -->
<div id="fieldResponsable" class="mb-3 d-none">
  <label for="responsable" class="form-label">Responsable</label>

  {{-- TomSelect busca remotamente en /api/clientes --}}
  <select id="responsable"
          name="cliente_id"   {{-- guarda el id del cliente --}}
          class="form-select"
          placeholder="Escribe para buscar…">
  </select>
</div>

<!-- Torneo: canchas múltiples -->
<div id="fieldCanchasMulti" class="mb-3 d-none">
  <label>Canchas</label>
  <select id="canchas" name="canchas[]" multiple class="form-select">
    @foreach(\App\Models\Cancha::all() as $c)
      <option value="{{ $c->id }}">{{ $c->nombre }}</option>
    @endforeach
  </select>
</div>

			
          </div>
          <div class="modal-footer">
            <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">
              Cancelar
            </button>
            <button type="submit" class="btn btn-primary" id="reservationSubmit">
              Guardar
            </button>
          </div>
        </form>
      </div>
    </div>
  </div>
 

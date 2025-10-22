@extends('layouts.vertical', ['subtitle' => 'Horario de Reservas'])
  <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.5/font/bootstrap-icons.css">
<link
  href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css"
  rel="stylesheet"
/>  
  <meta name="csrf-token" content="{{ csrf_token() }}">
@section('content')
<div class="p-6 bg-white rounded-lg shadow">
    <!-- Controles de navegación -->
    <div class="flex items-center justify-between mb-4">
        <div class="flex space-x-2">
            <a href="{{ route('reservas.horario', ['date' => $prevDate]) }}" class="ajax-nav px-4 py-2 bg-gray-200 hover:bg-gray-300 rounded">Anterior</a>
            <a href="{{ route('reservas.horario', ['date' => now()->toDateString()]) }}" class="ajax-nav px-4 py-2 bg-gray-200 hover:bg-gray-300 rounded">Hoy</a>
            <a href="{{ route('reservas.horario', ['date' => $nextDate]) }}" class="ajax-nav px-4 py-2 bg-gray-200 hover:bg-gray-300 rounded">Siguiente</a>
		    <a href="{{ route('reservas.calendar') }}" class="px-4 py-2 bg-gray-200 hover:bg-gray-300 rounded">Mes</a>
   
	   </div>
		

    </div>
	
	<button
  type="button"
  class="btn btn-primary mb-4"
  data-bs-toggle="modal"
  data-bs-target="#horarioReservationModal"
  id="openHorarioModalBtn"
>
  Nueva Reserva
</button>

    <!-- Contenedor de la tabla para AJAX -->
    <div id="schedule-container" class="container-fluid px-0">
        <table class="schedule-table" style="width:100%;">
		
		  <thead>
    <tr class="sticky top-0" style="background: linear-gradient(to right, #0053BF, #6366F1);">
        <th scope="col" 
            class="px-6 py-3 text-center text-xs font-semibold uppercase tracking-wider text-white">
         Hora
      </th>
      @foreach($canchas as $cancha)
        <th scope="col"
            class="px-6 py-3 text-center text-xs font-semibold uppercase tracking-wider text-white">
          {{ $cancha->nombre }}
        </th>
      @endforeach
    </tr>
  </thead>
		
            <tbody>
                @foreach($timeslots as $slot)
                    <tr class="hover:bg-gray-50">
                        <td class="border p-2 text-sm">{{ \Carbon\Carbon::parse($slot)->format('H:i') }}</td>
                        @foreach($canchas as $cancha)
                            @php
                                $event = $events[$cancha->id][$slot] ?? null;
                                // Mantener el fondo aunque no coincida el tipo exacto
								 if ($event) {
                                     if (($event->tipo) == 'Reserva') {
                                        $bg = 'rgba(96,66,245,0.35)';
                                    } elseif (($event->tipo) == 'Clase') {
                                        $bg = 'rgba(0,168,89,0.35)';
                                    } elseif (($event->tipo) == 'Torneo') {
                                        $bg = 'rgba(128,128,128,0.35)';
                                    } else {
                                        $bg = '';
                                    }
                                } else {
                                    $bg = '';
                                }
                            @endphp
						

                            <td style="background-color: {{$bg}}" class="border p-1 align-top">
  @if($event)
    <div class="d-flex justify-content-between align-items-center">
      <!-- Nombres de clientes y estado -->
      <div class="d-flex align-items-center">
        <a href="{{ route('reservas.edit', $event->id) }}">
          <span class="text-sm">
            {{ $event->clientes
                ->map(fn($a) => $a->nombres . ' ' . $a->apellidos)
                ->implode(', ')
            }}
          </span>
        </a>
        @php
          $badgeColor = strtolower($event->estado) === 'cobrada'
              ? 'bg-success'
              : 'bg-secondary';
        @endphp
        <span class="badge ms-1 {{ $badgeColor }}">{{ ucfirst($event->estado) }}</span>
      </div>
      <!-- Iconos de acción -->
      <div class="d-flex align-items-center">
        <!-- Editar -->




        <!-- Facturar -->
        <button type="button"
                class="btn p-0 text-green-500 hover:text-green-700 me-1"
                title="Facturar"
                onclick='facturarReserva({{ $event->id }}, @json($event->clientes->pluck("id")))'>
          <i class='bx bx-receipt'></i>
        </button>

        <!-- Anular (DELETE) -->
        <form action="{{ route('reservas.destroy', $event->id) }}"
              method="POST"
              class="ms-1"
              onsubmit="return confirm('¿Seguro que deseas anular esta reserva?');">
          @csrf
          @method('DELETE')
          <button type="submit"
                  class="btn p-0 text-red-500 hover:text-red-700"
                  title="Anular">
				  <i class='bx bx-trash'  ></i>
        </form>
      </div>
    </div>
  @endif
</td>

                        @endforeach
                    </tr>
                @endforeach
            </tbody>
        </table>
    </div>
</div>

 @include('reservas.partials.reservation-modal-horario')

@endsection





@vite('resources/js/app.js')

<script>
document.addEventListener('DOMContentLoaded', () => {
  const modalEl    = document.getElementById('horarioReservationModal');
  const form       = document.getElementById('horarioReservationForm');
  const methodInp  = document.getElementById('horarioReservationMethod');
  const dateInp    = document.getElementById('horarioFecha');
  const timeSel    = document.getElementById('horarioHora');
  const canchaSel  = document.getElementById('horarioCancha');
  const clientesSel = document.getElementById('clientes');

  if (!modalEl || !form || !methodInp || !dateInp || !timeSel || !canchaSel) {
    console.warn('[Horario] No se pudieron inicializar los eventos del modal porque faltan elementos requeridos.');
    return;
  }

  // Cuando se abra el modal desde el botón
  modalEl.addEventListener('show.bs.modal', event => {
    // Reset formulario
    methodInp.value = 'POST';
    form.action     = '{{ route("reservas.store") }}';

    // Fecha: hoy
    const hoy  = new Date();
    const yyyy = hoy.getFullYear();
    const mm   = String(hoy.getMonth()+1).padStart(2,'0');
    const dd   = String(hoy.getDate()).padStart(2,'0');
    dateInp.value = `${yyyy}-${mm}-${dd}`;

    // Limpiar selects
    timeSel.innerHTML    = '<option value="">-- Elige hora --</option>';
    canchaSel.selectedIndex = 0;
   

    // Opcional: cargar horas disponibles vía AJAX
    axios.get('/reserva/availability', {
      params: {
        date: dateInp.value,
        cancha_id: canchaSel.value
      }
    })
    .then(({ data }) => {
      // data.horas → array de strings "08:00", "08:30", …
      data.slots.forEach(h => {
        const opt = document.createElement('option');
        opt.value = h;
        opt.text  = h;
        timeSel.append(opt);
      });
    })
    .catch(err => console.error(err));
  });

  // Si cambian cancha o fecha, recargar horas
  [dateInp, canchaSel].forEach(el => {
    el.addEventListener('change', () => {
      // repetir misma llamada AJAX que arriba…
      axios.get('/reserva/availability', {
        params: {
          date: dateInp.value,
          cancha_id: canchaSel.value
        }
      })
      .then(({ data }) => {
        timeSel.innerHTML = '<option value="">-- Elige hora --</option>';
        data.slots.forEach(h => {
			 
          const opt = document.createElement('option');
     
	opt.value = h; opt.text = h;
          timeSel.append(opt);
        });
      })
      .catch(console.error);
    });
  });
});

const ventasIndexUrl = "{{ route('ventas.index') }}";
function facturarReserva(reservaId, clientes) {
  clientes.forEach(id => {
    window.open(`${ventasIndexUrl}?cliente_id=${id}`, '_blank');
  });

  axios.post(`/reservas/${reservaId}/cobrar`)
    .then(() => location.reload())
    .catch(console.error);
}

</script>

@push('scripts')
<script>
  $(function() {
    const $modal  = $('#horarioReservationModal');
    const $select = $('#clientes');
    let initDone  = false;

    // Inicializar solo una vez, cuando el modal ya esté visible
    $modal.on('shown.bs.modal', function() {
      if (initDone) return;
      initDone = true;

      $select.select2({
        placeholder: $select.data('placeholder'),
        allowClear: true,
		  dropdownParent: $modal.find('.modal-content'),
        		width: '100%'
      });
    });
  });
</script>
@endpush





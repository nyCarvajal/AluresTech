@extends('layouts.vertical', ['subtitle' => 'Calendario de Reservas'])


@section('content')

  <div class="card">
    <div class="card-header">
      <h5>Calendario de Clases y Reservas</h5>
    </div>

    <div class="mb-4 px-4 py-3 d-flex justify-content-between align-items-center">
      <a href="{{ route('reservas.horario', ['date' => now()->toDateString()]) }}"
         class="btn btn-info">Ir a Día</a>

      <div class="d-flex align-items-center">
        <label for="canchaFilter" class="form-label me-2 mb-0">Filtrar cancha:</label>
        <select id="canchaFilter"
                class="form-select shadow-sm rounded-pill ps-4 pe-5 border-0"
                style="min-width: 200px; background-color: #f8f9fa;">
          <option value="">Todas</option>
          @foreach($canchas as $cancha)
            <option value="{{ $cancha->id }}">{{ $cancha->nombre }}</option>
          @endforeach
        </select>
      </div>
    </div>

    <div class="wrapper-calendar" style="border-top:5px solid #D4A017;">
      <div id="calendar"></div>
    </div>

    @include('reservas.partials.reservation-modal')
  </div>
@endsection





@section('scripts')
  


  <script>
    document.addEventListener('DOMContentLoaded', () => {
      // FullCalendar ya se inicializa en app.js, solo envía la URL correcta:
      window.CalendarConfig = {
        selector: '#calendar',
        eventsUrl: '{{ route('reservas.events') }}',
        modalSelector: '#reservationModal'
      };
    });
  </script>
  {{-- Asegúrate de compilar app.js que importa FullCalendar --}}
  @vite('resources/js/app.js')
  
@endsection

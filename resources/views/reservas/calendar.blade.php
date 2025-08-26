@extends('layouts.vertical', ['subtitle' => 'Calendario de Reservas'])


@section('content')

  <div class="card">
    <div class="card-header d-flex flex-wrap justify-content-between align-items-center">
      <h5 class="mb-0">Calendario de Clases y Reservas</h5>
      <div class="mt-2 mt-sm-0">
        <select id="entrenadorFilter" class="form-select">
          <option value="">Todos los estilistas</option>
          @foreach($entrenadores as $e)
            <option value="{{ $e->id }}">{{ $e->nombre }}</option>
          @endforeach
        </select>
      </div>
    </div>



    <div class="wrapper-calendar w-100" style="border-top:5px solid #D4A017;">
      <div id="calendar" class="w-100"></div>
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
        modalSelector: '#reservationModal',
        filterSelector: '#entrenadorFilter'
      };
    });
  </script>
  {{-- Asegúrate de compilar app.js que importa FullCalendar --}}
  @vite('resources/js/app.js')
  
@endsection

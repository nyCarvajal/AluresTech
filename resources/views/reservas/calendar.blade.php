@extends('layouts.vertical', ['subtitle' => 'Calendario de Reservas'])


@section('content')

  <div class="card">
    <div class="card-header">
      <h5>Calendario de Clases y Reservas</h5>
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

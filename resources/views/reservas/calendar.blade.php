@extends('layouts.vertical', ['subtitle' => 'Calendario de Reservas'])

@push('styles')
  <style>
    .wrapper-calendar {
      min-height: 420px;
    }

    @media (max-width: 576px) {
      .wrapper-calendar,
      #calendar,
      #calendar .fc-view-harness,
      #calendar .fc-scrollgrid,
      #calendar .fc-view-harness-active {
        min-height: 520px;
      }
    }
  </style>
@endpush


@php
    $stylistLabelSingular = $stylistLabelSingular ?? \App\Models\Peluqueria::defaultRoleLabel(\App\Models\Peluqueria::ROLE_STYLIST);
    $stylistLabelPlural = $stylistLabelPlural ?? \App\Models\Peluqueria::defaultRoleLabel(\App\Models\Peluqueria::ROLE_STYLIST, true);
@endphp

@section('content')

  <div class="card">
    <div class="card-header d-flex flex-wrap justify-content-between align-items-center">
      <h5 class="mb-0">Calendario de Clases y Reservas</h5>
      <div class="mt-2 mt-sm-0">
        <select id="entrenadorFilter" class="form-select">
          <option value="">Todos los {{ \Illuminate\Support\Str::lower($stylistLabelPlural) }}</option>
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
    (function () {
      const calendarConfig = {
        selector: '#calendar',
        eventsUrl: '{{ route('reservas.events') }}',
        modalSelector: '#reservationModal',
        filterSelector: '#entrenadorFilter',
      };

      window.CalendarConfig = calendarConfig;

      const broadcastConfig = () => {
        try {
          window.dispatchEvent(new CustomEvent('alures:calendar-config-ready', { detail: calendarConfig }));
        } catch (error) {
          if (window.dispatchEvent && document.createEvent) {
            const fallbackEvent = document.createEvent('Event');
            fallbackEvent.initEvent('alures:calendar-config-ready', true, true);
            fallbackEvent.detail = calendarConfig;
            window.dispatchEvent(fallbackEvent);
          }
        }
      };

      window.setTimeout(() => {
        if (typeof window.bootstrapCalendar === 'function') {
          window.bootstrapCalendar(calendarConfig);
        } else {
          broadcastConfig();
        }
      }, 0);
    })();
  </script>
  {{-- Asegúrate de compilar app.js que importa FullCalendar --}}
  @vite('resources/js/app.js')
  
@endsection

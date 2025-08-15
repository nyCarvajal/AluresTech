@extends('layouts.vertical', ['subtitle' => 'Membresia'])


@section('content')
<div class="container">
    <h1 class="mb-4">Asociar Membresía/Paquete a Jugador</h1>

    <form action="{{ route('ventas.storememb') }}" method="POST">
      
        @csrf {{-- aunque es GET, puedes omitirlo --}}
        
        <div class="mb-3">
            <label for="jugador_id" class="form-label">Jugador</label>
            <select name="jugador_id" id="jugador_id" class="form-select" required>
                <option value="" disabled selected>-- Selecciona un jugador --</option>
                @foreach($jugadores as $jugador)
                    <option value="{{ $jugador->id }}">{{ $jugador->nombres }} {{ $jugador->apellidos }} </option>
                @endforeach
            </select>
        </div>

        <div class="mb-3">
            <label for="membresia_id" class="form-label">Membresía</label>
            <select name="membresia_id" id="membresia_id" class="form-select" required>
                <option value="" disabled selected>-- Selecciona una membresía --</option>
                @foreach($membresias as $membresia)
                    <option value="{{ $membresia->id }}">{{ $membresia->descripcion }}</option>
                @endforeach
            </select>
        </div>

       

        <button type="submit" class="btn btn-primary">
            Ir a Facturación
        </button>
    </form>
</div>
@endsection

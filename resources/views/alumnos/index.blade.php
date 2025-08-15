{{-- resources/views/alumnos/index.blade.php --}}
@extends('layouts.vertical', ['subtitle' => 'Alumnos'])

@section('css')
  <!-- Si necesitas algún CSS extra, añádelo aquí -->
@endsection

@section('content')
<div class="card">
  <div class="card-header">
    <h5 class="card-title">Lista de Alumnos</h5>
    <p class="card-subtitle">Todos los alumnos registrados en el sistema.</p>
  </div>
  <div class="card-header d-flex justify-content-between align-items-center">
    
    <a href="{{ route('alumnos.create') }}" class="btn btn-primary">Nuevo Alumno</a>
  </div>

  <div class="card-body">
    <div class="table-responsive">
      <table class="table table-hover table-centered">
        <thead class="table-light">
          <tr>
            <th scope="col">Alumno</th>
            <th scope="col">Correo</th>
            <th scope="col">WhatsApp</th>
            <th scope="col">Acciones</th>
          </tr>
        </thead>
        <tbody>
          @foreach($alumnos as $alumno)
            <tr>
              <td>
                <div class="d-flex align-items-center gap-1">
                  {{-- Avatar: sustituye con $alumno->avatar si lo tienes --}}
				  
				  <img
  src="{{ $alumno->foto
          ? 'https://res.cloudinary.com/dpikkji3p/image/upload/' . $alumno->foto . '.jpg'
          : ($alumno->sexo === 'F'
                ? asset('images/users/avatar-2.jpg')
                : asset('images/users/avatar-1.jpg')
            )
       }}"
  alt="Foto de {{ $alumno->nombre }}"

   class="avatar-sm rounded-circle">

                  <div class="d-block">
                    <h4 class="mb-0">{{ $alumno->nombres }} {{ $alumno->apellidos }}</h4>
					{{ $alumno->tipo_identificacion }} . {{ $alumno->numero_identificacion }}<br>
					
  @if($alumno->tipo==1)
    <h5>Socio</h5>
  @endif
	<h5>	Alumno </h5>
					 <span class="badge bg-success">{{ optional($alumno->nivel)->nivel ?? '—' }}</span>
                  </div>
                </div>
              </td>
              <td>{{ $alumno->correo }}</td>
              <td>
                @php
                  // Limpiar el número para wa.me
                  $clean = preg_replace('/\D+/', '', $alumno->whatsapp);
                @endphp
                <a href="https://wa.me/{{ $clean }}" target="_blank">
                  {{ $alumno->whatsapp }}
                </a>
              </td>
              <td>
                <a href="{{ route('alumnos.edit', $alumno) }}"
                   class="btn btn-primary btn-sm w-100">
                  Editar
                </a>
				<a href="{{ route('alumnos.show', $alumno) }}"
                   class="btn btn-success btn-sm w-100">
                  Ver
                </a>
              </td>
            </tr>
          @endforeach
        </tbody>
      </table>
    </div>

    {{-- Paginación, si la usas:
    <div class="mt-3">
      {{ $alumnos->links() }}
    </div>
    --}}
  </div>
</div>
@endsection

@section('scripts')
  <!-- Scripts adicionales si los necesitas -->
@endsection

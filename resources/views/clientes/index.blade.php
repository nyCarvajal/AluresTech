
{{-- resources/views/clientes/index.blade.php --}}

@extends('layouts.vertical', ['subtitle' => 'Clientes'])

@section('css')
  <!-- Si necesitas algún CSS extra, añádelo aquí -->
@endsection

@section('content')
<div class="card">
  <div class="card-header">
    <h5 class="card-title">Lista de Clientes</h5>
    <p class="card-subtitle">Todos los clientes registrados en el sistema.</p>
  </div>
  <div class="card-header d-flex justify-content-between align-items-center">
    <form action="{{ route('clientes.index') }}" method="GET" class="d-flex w-50">
      <input type="text" name="q" value="{{ request('q') }}" class="form-control me-2" placeholder="Buscar cliente...">
      <button type="submit" class="btn btn-secondary">Buscar</button>
    </form>
    <a href="{{ route('clientes.create') }}" class="btn btn-primary">Nuevo Cliente</a>
  </div>

  <div class="card-body">
    <div class="table-responsive">
      <table class="table table-hover table-centered">
        <thead class="table-light">
          <tr>
            <th scope="col">Cliente</th>
            <th scope="col">Correo</th>
            <th scope="col">WhatsApp</th>
            <th scope="col">Acciones</th>
          </tr>
        </thead>
        <tbody>
          @foreach($clientes as $cliente)
            <tr>
              <td>
                <div class="d-flex align-items-center gap-1">
                  {{-- Avatar: sustituye con $cliente->avatar si lo tienes --}}
				  
				  <img
  src="{{ $cliente->foto
          ? 'https://res.cloudinary.com/dpikkji3p/image/upload/' . $cliente->foto . '.jpg'
          : ($cliente->sexo === 'F'
                ? asset('images/users/avatar-2.jpg')
                : asset('images/users/avatar-1.jpg')
            )
       }}"
  alt="Foto de {{ $cliente->nombre }}"

   class="avatar-sm rounded-circle">

                  <div class="d-block">
                    <h4 class="mb-0">{{ $cliente->nombres }} {{ $cliente->apellidos }}</h4>
					{{ $cliente->tipo_identificacion }} . {{ $cliente->numero_identificacion }}<br>
					
  @if($cliente->tipo==1)
    <h5>Socio</h5>
  @endif
	<h5>	Cliente </h5>

					 

                  </div>
                </div>
              </td>
              <td>{{ $cliente->correo }}</td>
              <td>
                @php
                  // Limpiar el número para wa.me
                  $clean = preg_replace('/\D+/', '', $cliente->whatsapp);
                @endphp
                <a href="https://wa.me/{{ $clean }}" target="_blank">
                  {{ $cliente->whatsapp }}
                </a>
              </td>
              <td>
                <a href="{{ route('clientes.edit', $cliente) }}"
                   class="btn btn-primary btn-sm w-100">
                  Editar
                </a>
				<a href="{{ route('clientes.show', $cliente) }}"
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
      {{ $clientes->links() }}
    </div>
    --}}
  </div>
</div>
@endsection

@section('scripts')
  <!-- Scripts adicionales si los necesitas -->
@endsection

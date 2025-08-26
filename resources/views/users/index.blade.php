{{-- resources/views/users/index.blade.php --}}
@extends('layouts.vertical', ['subtitle' => 'Ver Usuarios'])



@section('content')

<div class="container">
@if(session('success'))
  <div class="alert alert-success">{{ session('success') }}</div>
@endif

  <div class="d-flex justify-content-between align-items-center mb-4">
    <h1>Usuarios</h1>
    <div>
      <a href="{{ route('users.trainers.create') }}" class="btn btn-primary me-2">
        + Nuevo Estilista
      </a>
      @if(auth()->user()->role == 18)
        <a href="{{ route('users.admins.create') }}" class="btn btn-success">
          + Nuevo Administrador
        </a>
      @endif
    </div>
  </div>

  @if(session('success'))
    <div class="alert alert-success">{{ session('success') }}</div>
  @endif
<div class="card card-body">
  <table class="table table-striped">
    <thead>
      <tr>
        <th>ID</th>
        <th>Nombre completo</th>
        <th>Email</th>
        <th>Rol</th>
        <th>Peluqueria</th>
        <th class="text-end">Acciones</th>
      </tr>
    </thead>
    <tbody>
      @forelse($users as $user)


        <tr>
          <td>{{ $user->id }}</td>
          <td>{{ $user->nombre }} {{ $user->apellidos }}</td>
          <td>{{ $user->email }}</td>
          <td>
		  
            @if($user->role == 11)
              Estilista
            @elseif($user->role == 18)
              Administrador
            @else
              {{ $user->role }}
            @endif
          </td>
          <td>{{ optional($user->peluqueria)->nombre ?? '—' }}</td>
          <td class="text-end">
            <a href="{{ route('users.edit', $user) }}" class="btn btn-sm btn-outline-primary">
              Editar
            </a>
            <form action="{{ route('users.destroy', $user->id) }}" method="POST" class="d-inline-block"
                  onsubmit="return confirm('¿Eliminar usuario #{{ $user->id }}?');">
              @csrf
              @method('DELETE')
              <button type="submit" class="btn btn-sm btn-outline-danger">
                Eliminar
              </button>
            </form>
          </td>
        </tr>
      @empty
        <tr>
          <td colspan="6" class="text-center">No hay usuarios registrados.</td>
        </tr>
      @endforelse
    </tbody>
  </table>
</div>
  @if(method_exists($users, 'links'))
    <div class="d-flex justify-content-center">
      {{ $users->links() }}
    </div>
  @endif
</div>
@endsection

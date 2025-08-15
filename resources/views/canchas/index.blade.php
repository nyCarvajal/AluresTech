{{-- resources/index/canchas/index.blade.php --}}
@extends('layouts.vertical', ['subtitle'=>'Canchas'])

@section('content')
<div class="card">
  <div class="card-header d-flex justify-content-between align-items-center">
    <h5 class="card-title mb-0">Listado de Canchas</h5>
    <a href="{{ route('canchas.create') }}" class="btn btn-primary">Nueva Cancha</a>
  </div>
  <div class="card-body table-responsive">
    <table class="table table-hover">
      <thead>
        <tr>
          <th>Nombre</th>
		  <th>Deporte</th>
          <th>Capacidad</th>
          <th>Precio/Hora</th>
          <th>Acciones</th>
        </tr>
      </thead>
      <tbody>
        @foreach($canchas as $c)
        <tr>
          <td>{{ $c->nombre }}</td>
		  <td>{{ optional($c->deporte)->deporte ?? '—' }}</td>
          <td>{{ $c->capacidad }}</td>
          <td>{{ number_format($c->valor,2) }}</td>
          <td class="d-flex gap-1">
            <a href="{{ route('canchas.edit',$c) }}" class="btn btn-sm btn-primary">Editar</a>
            <form action="{{ route('canchas.destroy',$c) }}" method="POST" onsubmit="return confirm('¿Eliminar?')">
              @csrf @method('DELETE')
              <button type="submit" class="btn btn-sm btn-danger">Borrar</button>
            </form>
          </td>
        </tr>
        @endforeach
      </tbody>
    </table>
  </div>
</div>
@endsection

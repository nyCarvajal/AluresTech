@extends('layouts.vertical', ['subtitle' => 'Editar Cancha'])

@section('content')
<div class="card">
  <div class="card-header"><h4>Editar Cancha</h4></div>
  <div class="card-body">
    <form action="{{ route('canchas.update', $cancha) }}" method="POST">
      @method('PUT')
      @include('canchas._form')
      <button class="btn btn-primary">Actualizar</button>
      <a href="{{ route('canchas.index') }}" class="btn btn-secondary ms-2">Cancelar</a>
    </form>
  </div>
</div>
@endsection

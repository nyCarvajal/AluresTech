@extends('layouts.vertical', ['subtitle' => 'Crear Cancha'])

@section('content')
<div class="card">
  <div class="card-header"><h4>Nueva Cancha</h4></div>
  <div class="card-body">
    <form action="{{ route('canchas.store') }}" method="POST">
      @include('canchas._form')
      <button class="btn btn-primary">Guardar</button>
      <a href="{{ route('canchas.index') }}" class="btn btn-secondary ms-2">Cancelar</a>
    </form>
  </div>
</div>
@endsection

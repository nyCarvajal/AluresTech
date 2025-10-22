@extends('layouts.vertical', ['subtitle' => 'Peluqueria'])

@section('content')
<div class="container">
  <h1>Editar Peluqueria</h1>
  <div class="alert alert-info" role="alert">
    ¿Quieres renombrar a tus {{ strtolower($stylistLabelPlural ?? 'Estilistas') }}?
    Dirígete a la sección <a href="#role-labels" class="alert-link">"Personaliza cómo llamas a tu equipo"</a> dentro de este formulario.
  </div>
  <form action="{{ route('peluquerias.update', $peluqueria) }}" method="POST">
    @csrf @method('PUT')
    @include('peluquerias.form')
    <button class="btn btn-success">Actualizar</button>
  </form>
</div>
@endsection

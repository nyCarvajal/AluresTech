@extends('layouts.vertical', ['subtitle' => 'Peluqueria'])

@section('content')
<div class="container">
  <h1>Editar Peluqueria</h1>
  <form action="{{ $formAction ?? route('peluquerias.update') }}" method="POST" enctype="multipart/form-data">
    @csrf @method('PUT')
    @include('peluquerias.form')
    <button class="btn btn-success">Actualizar</button>
  </form>
</div>
@endsection

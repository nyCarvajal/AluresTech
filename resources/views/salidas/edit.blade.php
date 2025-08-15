@extends('layouts.vertical', ['subtitle' => 'Gastos'])
   
@section('content')
<div class="card">
<div class="card-body">
  <h1>Editar Salida</h1>
  <form action="{{ route('salidas.update', $salida) }}" method="POST">
    @csrf @method('PUT')
    @include('salidas._form')
    <button class="btn btn-success">Actualizar</button>
  </form>
</div>
</div>
@endsection